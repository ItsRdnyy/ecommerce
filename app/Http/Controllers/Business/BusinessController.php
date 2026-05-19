<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\Category;
use App\Models\BulkPricing;
use App\Models\DiscountTier;
use App\Models\ShippingRule;
use App\Models\ProductVariant;
use App\Models\InventoryLog;
use App\Models\PreorderQueue;
use App\Models\Notification;
use App\Models\BusinessProfile;
use App\Services\InventoryManager;
use App\Services\NotificationService;
use App\Services\OrderStateMachine;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class BusinessController extends Controller
{
    public function index()
    {
        $business = auth()->user()->businessProfile;
        $businessId = auth()->id();

        $totalProducts = Product::where('business_id', $businessId)->count();
        $activeProducts = Product::where('business_id', $businessId)->where('status', 'active')->count();
        $outOfStock = Product::where('business_id', $businessId)->where('stock', 0)->count();

        $retailOrders = Order::where('business_id', $businessId)->where('type', 'retail')->count();
        $b2bOrders = Order::where('business_id', $businessId)->where('type', 'b2b')->count();
        $incomingRetail = Order::where('business_id', $businessId)->where('type', 'retail')->whereIn('status', ['pending', 'processing'])->count();
        $bulkRequests = Order::where('business_id', $businessId)->where('type', 'b2b')->whereIn('status', ['pending', 'processing'])->count();
        $toShip = Order::where('business_id', $businessId)->whereIn('status', ['processing', 'shipped'])->count();
        $shipped = Order::where('business_id', $businessId)->where('status', 'shipped')->count();

        $totalEarnings = Order::where('business_id', $businessId)->where('status', 'delivered')->sum('total');
        $retailSales = Order::where('business_id', $businessId)->where('type', 'retail')->where('status', 'delivered')->count();
        $b2bSales = Order::where('business_id', $businessId)->where('type', 'b2b')->where('status', 'delivered')->count();
        $pendingCommission = Order::where('business_id', $businessId)->where('status', 'delivered')->sum('commission');
        $availableEarnings = $totalEarnings - $pendingCommission;

        $bestSellingProduct = Product::where('business_id', $businessId)
            ->withCount('orderItems')
            ->orderBy('order_items_count', 'desc')
            ->first();

        $soloBuyerCustomers = Order::where('business_id', $businessId)
            ->whereHas('buyer', function ($q) {
                $q->where('role', 'buyer');
            })
            ->distinct('buyer_id')
            ->count();

        $businessCustomers = Order::where('business_id', $businessId)
            ->whereHas('buyer', function ($q) {
                $q->where('role', 'business');
            })
            ->distinct('buyer_id')
            ->count();

        $wholesaleListings = Product::where('business_id', $businessId)->where('wholesale_price', '>', 0)->count();

        $recentOrders = Order::where('business_id', $businessId)
            ->latest()
            ->take(3)
            ->get();

        return view('business.dashboard', compact(
            'business',
            'totalProducts',
            'activeProducts',
            'outOfStock',
            'retailOrders',
            'b2bOrders',
            'incomingRetail',
            'bulkRequests',
            'toShip',
            'shipped',
            'totalEarnings',
            'retailSales',
            'b2bSales',
            'pendingCommission',
            'availableEarnings',
            'bestSellingProduct',
            'soloBuyerCustomers',
            'businessCustomers',
            'wholesaleListings',
            'recentOrders'
        ));
    }

    public function products(Request $request)
    {
        $businessId = auth()->id();
        $query = Product::where('business_id', $businessId)
            ->whereIn('status', ['active', 'flagged'])
            ->with(['category', 'variants']);
            
        // Filter by category if selected
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }
        
        $products = $query->latest()->get();
        $categories = Category::all();
        return view('business.products', compact('products', 'categories'));
    }

    public function filterProducts(Request $request)
    {
        $businessId = auth()->id();
        $query = Product::where('business_id', $businessId)
            ->whereIn('status', ['active', 'flagged'])
            ->with(['category', 'variants']);

        // Filter by category if selected
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter by gender if selected
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        $products = $query->latest()->get();
        $categories = Category::all();

        // If AJAX request, return only the products grid
        if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return view('business.products_grid', compact('products'))->render();
        }

        return view('business.products', compact('products', 'categories'));
    }

    public function storeProduct(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'retail_price' => 'required|numeric|min:0',
            'wholesale_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive,flagged',
            'gender' => 'required|in:men,women,unisex',
            'image' => 'nullable|file|max:10240',
            'bulk_min_quantity' => 'array',
            'bulk_min_quantity.*' => 'nullable|integer|min:1',
            'bulk_max_quantity' => 'array',
            'bulk_max_quantity.*' => 'nullable|integer|min:1',
            'sizes' => 'nullable|string|max:255',
            'sizes_stock' => 'nullable|array',
            'sizes_stock.*' => 'nullable|integer|min:0',
        ]);

        $imagePath = null;
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $product = Product::create([
            'business_id' => auth()->id(),
            'name' => $validated['name'],
            'description' => $validated['description'] ?? '',
            'category_id' => $validated['category_id'],
            'retail_price' => $validated['retail_price'],
            'wholesale_price' => $validated['wholesale_price'] ?? null,
            'stock' => $validated['stock'],
            'status' => $validated['status'],
            'gender' => $validated['gender'],
            'image' => $imagePath,
        ]);

        // Save bulk pricing tiers
        if ($request->has('bulk_min_quantity')) {
            foreach ($request->bulk_min_quantity as $key => $minQty) {
                if (!empty($minQty)) {
                    BulkPricing::create([
                        'product_id' => $product->id,
                        'min_quantity' => $minQty,
                        'max_quantity' => $request->bulk_max_quantity[$key] ?? null,
                    ]);
                }
            }
        }

        // Create variants for sizes if provided
        if ($request->filled('sizes')) {
            $sizes = array_filter(array_map('trim', explode(',', $request->sizes)));
            $sizesStock = $request->input('sizes_stock', []);
            $sizesPrice = $request->input('sizes_price', []);

            foreach ($sizes as $size) {
                if (!empty($size)) {
                    $stock = $sizesStock[$size] ?? 0;
                    $price = isset($sizesPrice[$size]) && $sizesPrice[$size] !== '' ? floatval($sizesPrice[$size]) : null;
                    ProductVariant::create([
                        'product_id' => $product->id,
                        'name' => $product->name . ' - ' . $size,
                        'attributes' => ['size' => $size],
                        'stock' => $stock,
                        'price' => $price,
                    ]);
                }
            }
        }

        return redirect()->route('business.products')->with('success', 'Product created successfully.');
    }

    public function editProduct(Product $product)
    {
        if ($product->business_id !== auth()->id()) {
            abort(403);
        }

        $product->load('variants');
        $sizes = $product->variants->map(fn($v) => $v->attributes['size'] ?? null)->filter()->unique()->values()->toArray();
        $product->sizes_string = implode(', ', $sizes);
        $product->variants = $product->variants->map(function($variant) {
            return [
                'id' => $variant->id,
                'attributes' => $variant->attributes,
                'stock' => $variant->stock,
                'price' => $variant->price,
            ];
        });

        return response()->json($product);
    }

    public function updateProduct(Request $request, Product $product)
    {
        if ($product->business_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'retail_price' => 'required|numeric|min:0',
            'wholesale_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive,flagged',
            'gender' => 'required|in:men,women,unisex',
            'image' => 'nullable|file|max:10240',
            'sizes' => 'nullable|string|max:255',
            'sizes_stock' => 'nullable|array',
            'sizes_stock.*' => 'nullable|integer|min:0',
            'sizes_price' => 'nullable|array',
            'sizes_price.*' => 'nullable|numeric|min:0',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'description' => $validated['description'] ?? '',
            'category_id' => $validated['category_id'],
            'retail_price' => $validated['retail_price'],
            'wholesale_price' => $validated['wholesale_price'] ?? null,
            'stock' => $validated['stock'],
            'status' => $validated['status'],
            'gender' => $validated['gender'],
        ];

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $imagePath = $request->file('image')->store('products', 'public');
            $updateData['image'] = $imagePath;
        }

        $product->update($updateData);

        if ($request->filled('sizes')) {
            $sizes = array_filter(array_map('trim', explode(',', $request->sizes)));
            $sizesStock = $request->input('sizes_stock', []);
            $sizesPrice = $request->input('sizes_price', []);
            $existingVariants = $product->variants()->get();
            $existingSizes = $existingVariants->map(fn($v) => $v->attributes['size'] ?? null)->filter()->values()->toArray();

            // Update existing variants stock and price
            foreach ($existingVariants as $variant) {
                $size = $variant->attributes['size'] ?? null;
                if ($size && in_array($size, $sizes)) {
                    $variant->stock = $sizesStock[$size] ?? 0;
                    $variant->price = isset($sizesPrice[$size]) && $sizesPrice[$size] !== '' ? floatval($sizesPrice[$size]) : null;
                    $variant->save();
                }
            }

            // Create new variants for new sizes
            $newSizes = array_diff($sizes, $existingSizes);
            foreach ($newSizes as $size) {
                if (!empty($size)) {
                    $price = isset($sizesPrice[$size]) && $sizesPrice[$size] !== '' ? floatval($sizesPrice[$size]) : null;
                    ProductVariant::create([
                        'product_id' => $product->id,
                        'name' => $product->name . ' - ' . $size,
                        'attributes' => ['size' => $size],
                        'stock' => $sizesStock[$size] ?? 0,
                        'price' => $price,
                    ]);
                }
            }

            // Delete variants for removed sizes
            $removedSizes = array_diff($existingSizes, $sizes);
            foreach ($removedSizes as $size) {
                $product->variants()->where('attributes->size', $size)->delete();
            }
        }

        return redirect()->route('business.products')->with('success', 'Product updated successfully.');
    }

    public function archiveProduct(Request $request, Product $product)
    {
        if ($product->business_id !== auth()->id()) {
            abort(403);
        }

        $product->status = 'inactive';
        $product->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Product archived successfully']);
        }

        return redirect()->route('business.products')->with('success', 'Product archived successfully.');
    }

    public function archivedProducts()
    {
        $businessId = auth()->id();
        $archivedProducts = Product::where('business_id', $businessId)
            ->where('status', 'inactive')
            ->with('category')
            ->latest()
            ->get();

        return view('business.archived', compact('archivedProducts'));
    }

    public function restoreProduct(Request $request, Product $product)
    {
        if ($product->business_id !== auth()->id()) {
            abort(403);
        }

        $product->status = 'active';
        $product->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Product restored successfully']);
        }

        return redirect()->route('business.products.archived')->with('success', 'Product restored successfully.');
    }

    public function deleteProduct(Request $request, Product $product)
    {
        if ($product->business_id !== auth()->id()) {
            abort(403);
        }

        $product->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Product deleted successfully']);
        }

        return redirect()->route('business.products.archived')->with('success', 'Product deleted successfully.');
    }

    public function orders()
    {
        $businessId = auth()->id();

        $retailOrders = Order::where('business_id', $businessId)
            ->where('type', 'retail')
            ->with('buyer', 'items.product')
            ->orderByDesc('created_at')
            ->get();

        $b2bOrders = Order::where('business_id', $businessId)
            ->where('type', 'b2b')
            ->with('buyer', 'items.product')
            ->orderByDesc('created_at')
            ->get();

        return view('business.orders', compact('retailOrders', 'b2bOrders'));
    }

    public function updateOrderStatus(Request $request, Order $order)
    {
        if ($order->business_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
        ]);

        $order->update(['status' => $validated['status']]);

        return redirect()->route('business.orders')->with('success', 'Order status updated successfully.');
    }

    public function sales()
    {
        $businessId = auth()->id();

        $orders = Order::where('business_id', $businessId)
            ->with('buyer', 'items.product')
            ->orderByDesc('created_at')
            ->paginate(20);

        $totalRevenue = Order::where('business_id', $businessId)
            ->whereIn('status', [Order::STATUS_DELIVERED, Order::STATUS_COMPLETED])
            ->sum('total');

        $totalCommission = Order::where('business_id', $businessId)
            ->sum('commission');

        $netEarnings = $totalRevenue - $totalCommission;

        return view('business.sales', compact('orders', 'totalRevenue', 'totalCommission', 'netEarnings'));
    }

    public function discountTiers()
    {
        $tiers = DiscountTier::where('business_id', auth()->id())
            ->with('product')
            ->orderByDesc('created_at')
            ->get();

        $products = Product::where('business_id', auth()->id())->where('status', 'active')->get();

        return view('business.discount_tiers', compact('tiers', 'products'));
    }

    public function storeDiscountTier(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'nullable|exists:products,id',
            'min_quantity' => 'required|integer|min:1',
            'max_quantity' => 'nullable|integer|min:1',
            'discount_percent' => 'required|numeric|min:0|max:100',
        ]);

        $product = $validated['product_id'] ? Product::find($validated['product_id']) : null;
        if ($product && $product->business_id !== auth()->id()) {
            abort(403);
        }

        DiscountTier::create([
            'business_id' => auth()->id(),
            'product_id' => $validated['product_id'] ?? null,
            'min_quantity' => $validated['min_quantity'],
            'max_quantity' => $validated['max_quantity'] ?? null,
            'discount_percent' => $validated['discount_percent'],
        ]);

        return back()->with('success', 'Discount tier created.');
    }

    public function toggleDiscountTier(DiscountTier $tier)
    {
        if ($tier->business_id !== auth()->id()) {
            abort(403);
        }

        $tier->update(['is_active' => !$tier->is_active]);
        return back()->with('success', 'Discount tier updated.');
    }

    public function shippingRules()
    {
        $rules = ShippingRule::where('business_id', auth()->id())
            ->orderByDesc('created_at')
            ->get();

        return view('business.shipping_rules', compact('rules'));
    }

    public function storeShippingRule(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:local,national,international',
            'base_rate' => 'required|numeric|min:0',
            'weight_rate' => 'required|numeric|min:0',
            'distance_rate' => 'required|numeric|min:0',
            'handling_fee' => 'required|numeric|min:0',
            'min_weight' => 'nullable|numeric|min:0',
            'max_weight' => 'nullable|numeric|min:0',
            'regions' => 'nullable|array',
            'couriers' => 'nullable|array',
        ]);

        ShippingRule::create([
            'business_id' => auth()->id(),
            'name' => $validated['name'],
            'type' => $validated['type'],
            'base_rate' => $validated['base_rate'],
            'weight_rate' => $validated['weight_rate'],
            'distance_rate' => $validated['distance_rate'],
            'handling_fee' => $validated['handling_fee'],
            'min_weight' => $validated['min_weight'] ?? null,
            'max_weight' => $validated['max_weight'] ?? null,
            'regions' => $validated['regions'] ?? null,
            'couriers' => $validated['couriers'] ?? null,
        ]);

        return back()->with('success', 'Shipping rule created.');
    }

    public function toggleShippingRule(ShippingRule $rule)
    {
        if ($rule->business_id !== auth()->id()) {
            abort(403);
        }

        $rule->update(['is_active' => !$rule->is_active]);
        return back()->with('success', 'Shipping rule updated.');
    }

    public function inventory()
    {
        $businessId = auth()->id();

        $products = Product::where('business_id', $businessId)
            ->with('variants')
            ->orderByDesc('created_at')
            ->get();

        $lowStock = Product::where('business_id', $businessId)
            ->whereRaw('stock - COALESCE(reserved_stock, 0) <= 10')
            ->where('status', 'active')
            ->get();

        $logs = InventoryLog::whereHas('product', function ($q) use ($businessId) {
            $q->where('business_id', $businessId);
        })->latest()->limit(100)->get();

        return view('business.inventory', compact('products', 'lowStock', 'logs'));
    }

    public function updateInventory(Request $request, Product $product)
    {
        if ($product->business_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'quantity' => 'required|integer',
            'reason' => 'required|string|max:255',
        ]);

        $quantity = (int) $validated['quantity'];

        if ($quantity > 0) {
            InventoryManager::restock($product, $quantity, $validated['reason'], auth()->id());
        } elseif ($quantity < 0) {
            $deduct = abs($quantity);
            if ($product->stock < $deduct) {
                return back()->with('error', 'Insufficient stock to deduct.');
            }
            $product->stock -= $deduct;
            $product->save();

            InventoryLog::create([
                'product_id' => $product->id,
                'action' => 'adjustment',
                'quantity_change' => -$deduct,
                'stock_after' => $product->stock,
                'reserved_after' => $product->reserved_stock ?? 0,
                'reason' => $validated['reason'],
                'user_id' => auth()->id(),
            ]);
        }

        return back()->with('success', 'Inventory updated.');
    }

    public function preorders()
    {
        $businessId = auth()->id();

        $preorders = PreorderQueue::whereHas('product', function ($q) use ($businessId) {
            $q->where('business_id', $businessId);
        })
        ->with('product', 'user')
        ->orderByDesc('created_at')
        ->paginate(20);

        return view('business.preorders', compact('preorders'));
    }

    public function fulfillPreorder(PreorderQueue $preorder)
    {
        $product = $preorder->product;
        if ($product->business_id !== auth()->id()) {
            abort(403);
        }

        DB::beginTransaction();
        try {
            $preorder->update([
                'status' => 'fulfilled',
                'fulfilled_at' => now(),
            ]);

            $product->reserved_stock = max(0, ($product->reserved_stock ?? 0) - $preorder->quantity);
            $product->stock -= $preorder->quantity;
            $product->save();

            InventoryLog::create([
                'product_id' => $product->id,
                'action' => 'preorder_fulfill',
                'quantity_change' => -$preorder->quantity,
                'stock_after' => $product->stock,
                'reserved_after' => $product->reserved_stock,
                'reason' => 'Preorder fulfillment #' . $preorder->id,
                'order_id' => null,
                'user_id' => auth()->id(),
            ]);

            NotificationService::notify(
                $preorder->user,
                'preorder_update',
                'Preorder Fulfilled',
                "Your preorder for {$product->name} has been fulfilled and is ready for shipping.",
                ['preorder_id' => $preorder->id, 'product_id' => $product->id]
            );

            DB::commit();
            return back()->with('success', 'Preorder fulfilled.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function productVariants(Product $product)
    {
        if ($product->business_id !== auth()->id()) {
            abort(403);
        }

        $variants = ProductVariant::where('product_id', $product->id)->get();
        return view('business.variants', compact('product', 'variants'));
    }

    public function storeVariant(Request $request, Product $product)
    {
        if ($product->business_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:255',
            'attributes' => 'nullable|array',
            'image' => 'nullable|file|max:10240',
        ]);

        $imagePath = null;
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $imagePath = $request->file('image')->store('variants', 'public');
        }

        ProductVariant::create([
            'product_id' => $product->id,
            'name' => $validated['name'],
            'sku' => $validated['sku'] ?? null,
            'attributes' => $validated['attributes'] ?? null,
            'image' => $imagePath,
        ]);

        return back()->with('success', 'Variant created.');
    }

    public function profile()
    {
        $business = auth()->user()->businessProfile;
        return view('business.profile', compact('business'));
    }

    public function updateProfile(Request $request)
    {
        $business = auth()->user()->businessProfile;

        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'business_address' => 'nullable|string|max:255',
            'business_phone' => 'nullable|string|max:20',
            'tax_id' => 'nullable|string|max:50',
            'logo' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,webp',
        ]);

        $updateData = [
            'business_name' => $validated['business_name'],
            'business_address' => $validated['business_address'] ?? null,
            'business_phone' => $validated['business_phone'] ?? null,
            'tax_id' => $validated['tax_id'] ?? null,
        ];

        if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
            if ($business && $business->logo && Storage::disk('public')->exists($business->logo)) {
                Storage::disk('public')->delete($business->logo);
            }
            $logoPath = $request->file('logo')->store('business-logos', 'public');
            $updateData['logo'] = $logoPath;
        }

        if ($business) {
            $business->update($updateData);
        } else {
            $updateData['user_id'] = auth()->id();
            BusinessProfile::create($updateData);
        }

        return back()->with('success', 'Business profile updated successfully.');
    }

    public function settings()
    {
        $user = auth()->user();
        return view('business.settings', compact('user'));
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->with('error', 'Current password is incorrect.');
        }

        $user->password = Hash::make($validated['password']);
        $user->save();

        return back()->with('success', 'Password updated successfully.');
    }
}
