<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category', 'business.businessProfile', 'variants')->where('status', 'active');

        // Search functionality
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Category filtering
        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }

        // Gender filtering
        if ($request->has('gender') && $request->gender) {
            $query->where('gender', $request->gender);
        }

        // Preserve original product order by ID ascending (no sorting applied during filtering)
        $products = $query->orderBy('id', 'asc')->get();
        $productsByShop = $products->groupBy('business_id');
        $categories = Category::all();
        $selectedCategory = $request->category;
        $selectedGender = $request->gender;

        // Return JSON for AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'products' => $products->map(function ($product) {
                    $catName = strtolower($product->category->name ?? '');
                    $isApparelOrShoe = str_contains($catName, 'clothing') || str_contains($catName, 'shirt') || str_contains($catName, 'pants') || str_contains($catName, 'dress') || str_contains($catName, 'apparel') || str_contains($catName, 'shoe') || str_contains($catName, 'footwear') || str_contains($catName, 'sneaker') || str_contains($catName, 'boot');
                    $totalStock = $isApparelOrShoe ? $product->variants->sum('stock') : $product->stock;
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'description' => $product->description,
                        'retail_price' => $product->retail_price,
                        'wholesale_price' => $product->wholesale_price,
                        'moq' => $product->moq,
                        'is_wholesale_enabled' => $product->is_wholesale_enabled,
                        'stock' => $totalStock,
                        'variants' => $product->variants->map(function ($variant) {
                            return [
                                'size' => $variant->attributes['size'] ?? null,
                                'stock' => $variant->stock,
                                'price' => $variant->price
                            ];
                        })->toArray(),
                        'image' => $product->image,
                        'category_name' => $product->category ? $product->category->name : 'General',
                        'gender' => $product->gender,
                        'business_id' => $product->business_id,
                        'business_name' => $product->business && $product->business->businessProfile 
                            ? $product->business->businessProfile->business_name 
                            : ($product->business ? $product->business->name : 'Unknown Shop'),
                    ];
                })
            ]);
        }

        return view('products', compact('products', 'productsByShop', 'categories', 'selectedCategory', 'selectedGender'));
    }

    public function show(Product $product)
    {
        $product->load('category');
        return view('product-detail', compact('product'));
    }

    public function calculatePrice(Request $request, Product $product)
    {
        $quantity = (int) $request->input('quantity', 1);
        $size = $request->input('size');

        $type = 'retail';
        $user = auth()->user();
        if ($user && $user->isBusiness() && $product->is_wholesale_enabled && $product->wholesale_price > 0) {
            if (\App\Services\DiscountEngine::validateMoq($product, $quantity, 'wholesale')) {
                $type = 'wholesale';
            }
        }

        $calc = \App\Services\DiscountEngine::calculate($product, $quantity, $type, $size);

        return response()->json([
            'success' => true,
            'base_price' => $calc['base_price'],
            'quantity' => $calc['quantity'],
            'discount_rate' => $calc['discount_rate'],
            'discount_percent' => $calc['discount_rate'] * 100,
            'discount_amount' => $calc['discount_amount'],
            'unit_price' => $calc['unit_price'],
            'total' => $calc['total'],
            'type' => $type,
            'formatted_base_price' => number_format($calc['base_price'], 2),
            'formatted_discount_amount' => number_format($calc['discount_amount'], 2),
            'formatted_unit_price' => number_format($calc['unit_price'], 2),
            'formatted_total' => number_format($calc['total'], 2),
        ]);
    }
}

