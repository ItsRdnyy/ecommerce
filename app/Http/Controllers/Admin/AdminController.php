<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\Dispute;
use App\Models\Category;
use App\Models\Setting;
use App\Models\BusinessProfile;
use App\Models\Review;
use App\Models\TransactionLog;
use App\Events\UserApproved;

class AdminController extends Controller
{
    public function index()
    {
        $totalUsers = User::where('role', User::ROLE_BUSINESS)->count();
        $totalBusinesses = User::where('role', User::ROLE_BUSINESS)->count();
        $totalProducts = Product::count();
        $retailOrders = Order::where('type', 'retail')->count();
        $b2bOrders = Order::where('type', 'b2b')->count();
        $totalRevenue = Order::sum('commission') + Order::sum('platform_fee');

        $users = User::where('role', User::ROLE_BUSINESS)
            ->orderBy('name')->get();

        $openDisputes = Dispute::where('status', 'open')->count();
        $pendingVerifications = BusinessProfile::whereNull('verified_at')->count();

        return view('admin.dashboard', compact(
            'users', 'totalUsers', 'totalBusinesses',
            'totalProducts', 'retailOrders', 'b2bOrders', 'totalRevenue',
            'openDisputes', 'pendingVerifications'
        ));
    }

    public function businesses()
    {
        $businesses = User::where('role', User::ROLE_BUSINESS)
            ->with('businessProfile')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.businesses', compact('businesses'));
    }

    public function products()
    {
        $products = Product::with('business', 'category')
            ->orderBy('created_at', 'desc')
            ->get();
        $categories = Category::all();

        return view('admin.products', compact('products', 'categories'));
    }

    public function updateProductStatus(Request $request, Product $product)
    {
        $product->update(['status' => $request->status]);
        return back()->with('success', 'Product status updated.');
    }

    public function revenue()
    {
        $commissionRate = Setting::get('commission_rate', '10');
        $platformFee = Setting::get('platform_fee', '2.50');

        $totalCommission = Order::sum('commission');
        $totalPlatformFees = Order::sum('platform_fee');
        $totalRevenue = $totalCommission + $totalPlatformFees;

        $retailRevenue = Order::where('type', 'retail')->sum('total');
        $b2bRevenue = Order::where('type', 'b2b')->sum('total');
        $pendingPayouts = Order::where('status', '!=', 'cancelled')->sum('total') - $totalCommission - $totalPlatformFees;

        return view('admin.revenue', compact(
            'commissionRate', 'platformFee', 'totalCommission',
            'totalPlatformFees', 'totalRevenue', 'retailRevenue',
            'b2bRevenue', 'pendingPayouts'
        ));
    }

    public function updateRevenueSettings(Request $request)
    {
        $request->validate([
            'commission_rate' => 'required|numeric|min:0|max:100',
            'platform_fee' => 'required|numeric|min:0',
        ]);

        Setting::set('commission_rate', $request->commission_rate);
        Setting::set('platform_fee', $request->platform_fee);

        return back()->with('success', 'Revenue settings updated.');
    }

    public function orders()
    {
        $retailOrders = Order::where('type', 'retail')
            ->with('buyer', 'business', 'items.product', 'payments')
            ->orderBy('created_at', 'desc')
            ->get();

        $b2bOrders = Order::where('type', 'b2b')
            ->with('buyer', 'business', 'items.product', 'payments')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.orders', compact('retailOrders', 'b2bOrders'));
    }

    public function updateOrderStatus(Request $request, Order $order)
    {
        $newStatus = $request->status;
        $currentStatus = $order->status;

        $allowedTransitions = [
            'pending' => ['processing', 'cancelled'],
            'processing' => ['shipped', 'cancelled'],
            'shipped' => [],
            'delivered' => [],
            'cancelled' => [],
        ];

        if (!in_array($newStatus, $allowedTransitions[$currentStatus] ?? [])) {
            return back()->with('error', 'Invalid status transition.');
        }

        $order->update(['status' => $newStatus]);
        return back()->with('success', 'Order status updated.');
    }

    public function disputes()
    {
        $disputes = Dispute::with('order', 'user', 'business')
            ->orderBy('created_at', 'desc')
            ->get();

        $openCount = Dispute::where('status', 'open')->count();
        $resolvedCount = Dispute::where('status', 'resolved')->count();

        return view('admin.disputes', compact('disputes', 'openCount', 'resolvedCount'));
    }

    public function updateDispute(Request $request, Dispute $dispute)
    {
        $request->validate([
            'status' => 'required|in:open,resolved,closed',
            'resolution' => 'nullable|string',
        ]);

        $dispute->update([
            'status' => $request->status,
            'resolution' => $request->resolution,
        ]);

        return back()->with('success', 'Dispute updated.');
    }

    public function users()
    {
        $businesses = User::where('role', '!=', User::ROLE_ADMIN)
            ->with('businessProfile')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.users', compact('businesses'));
    }

    public function toggleUserStatus(Request $request, User $user)
    {
        $user->update(['status' => $request->status]);
        return back()->with('success', 'User status updated.');
    }

    public function approveUser(User $user)
    {
        event(new UserApproved($user));

        return back()->with('success', 'User account approved and verification code sent.');
    }

    public function rejectUser(User $user)
    {
        $user->update(['status' => User::STATUS_REJECTED]);
        return back()->with('success', 'User account rejected.');
    }

    public function analytics()
    {
        $topBusiness = User::where('role', User::ROLE_BUSINESS)
            ->withCount('ordersAsBusiness')
            ->orderBy('orders_as_business_count', 'desc')
            ->first();

        $topProduct = Product::withCount('orderItems')
            ->orderBy('order_items_count', 'desc')
            ->first();

        $totalOrders = Order::count();
        $totalUsers = User::where('role', User::ROLE_BUSINESS)->count();
        $conversionRate = $totalUsers > 0 ? round(($totalOrders / $totalUsers) * 100, 1) : 0;

        $retailGrowth = Order::where('type', 'retail')->count();
        $b2bGrowth = Order::where('type', 'b2b')->count();

        $totalSales = Order::whereIn('status', [Order::STATUS_DELIVERED, Order::STATUS_COMPLETED])->sum('total');
        $totalCommission = Order::sum('commission') + Order::sum('platform_fee');
        $activeBusinesses = User::where('role', User::ROLE_BUSINESS)->where('status', 'active')->count();
        $pendingDisputes = Dispute::where('status', 'open')->count();

        $monthlyRevenue = Order::where('created_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(created_at) as date, SUM(total) as revenue, SUM(commission + platform_fee) as commission')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $topCategories = \App\Services\RecommendationEngine::bestSellingCategories(10);

        return view('admin.analytics', compact(
            'topBusiness', 'topProduct',
            'conversionRate', 'retailGrowth', 'b2bGrowth',
            'totalSales', 'totalCommission', 'activeBusinesses',
            'pendingDisputes', 'monthlyRevenue', 'topCategories'
        ));
    }

    public function reviews()
    {
        $reviews = Review::with('product', 'user')
            ->orderByDesc('created_at')
            ->paginate(50);

        return view('admin.reviews', compact('reviews'));
    }

    public function updateReviewStatus(Request $request, Review $review)
    {
        $request->validate(['status' => 'required|in:pending,approved,rejected']);
        $review->update(['status' => $request->status]);
        return back()->with('success', 'Review status updated.');
    }

    public function transactionLogs()
    {
        $logs = TransactionLog::with('user')
            ->orderByDesc('created_at')
            ->paginate(50);

        return view('admin.transactions', compact('logs'));
    }

    public function verifications()
    {
        $profiles = BusinessProfile::with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.verifications', compact('profiles'));
    }

    public function updateVerification(Request $request, BusinessProfile $profile)
    {
        if ($request->action === 'verify') {
            $profile->update(['verified_at' => now()]);
        } elseif ($request->action === 'reject') {
            $profile->update(['verified_at' => null, 'rejected_at' => now()]);
        }

        return back()->with('success', 'Verification updated.');
    }

    public function settings()
    {
        $settings = [
            'commission_rate' => Setting::get('commission_rate', '10'),
            'platform_fee' => Setting::get('platform_fee', '2.50'),
            'free_shipping_threshold' => Setting::get('free_shipping_threshold', '75'),
            'solo_buyer_max' => Setting::get('solo_buyer_max', '3'),
            'currency' => Setting::get('currency', 'USD'),
        ];

        return view('admin.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'commission_rate' => 'required|numeric|min:0|max:100',
            'platform_fee' => 'required|numeric|min:0',
            'free_shipping_threshold' => 'required|numeric|min:0',
            'solo_buyer_max' => 'required|integer|min:1',
            'currency' => 'required|string|max:5',
        ]);

        Setting::set('commission_rate', $request->commission_rate);
        Setting::set('platform_fee', $request->platform_fee);
        Setting::set('free_shipping_threshold', $request->free_shipping_threshold);
        Setting::set('solo_buyer_max', $request->solo_buyer_max);
        Setting::set('currency', $request->currency);

        return back()->with('success', 'Settings saved successfully.');
    }
}
