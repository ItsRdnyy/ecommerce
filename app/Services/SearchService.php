<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;

class SearchService
{
    public static function searchProducts(array $filters = []): Builder
    {
        $query = Product::with(['business.businessProfile', 'category', 'variants'])
            ->where('status', 'active');

        if (!empty($filters['q'])) {
            $search = $filters['q'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['business_id'])) {
            $query->where('business_id', $filters['business_id']);
        }

        if (!empty($filters['min_price'])) {
            $query->where('retail_price', '>=', $filters['min_price']);
        }

        if (!empty($filters['max_price'])) {
            $query->where('retail_price', '<=', $filters['max_price']);
        }

        if (!empty($filters['gender'])) {
            $query->where('gender', $filters['gender']);
        }

        if (!empty($filters['moq'])) {
            $query->where('moq', '<=', $filters['moq']);
        }

        if (!empty($filters['wholesale'])) {
            $query->where('is_wholesale_enabled', true)
                  ->where('wholesale_price', '>', 0);
        }

        if (!empty($filters['preorder'])) {
            $query->where('is_preorder', true);
        }

        if (!empty($filters['in_stock'])) {
            $query->whereRaw('stock - COALESCE(reserved_stock, 0) > 0');
        }

        if (!empty($filters['size']) || !empty($filters['color'])) {
            $query->whereHas('variants', function ($q) use ($filters) {
                if (!empty($filters['size'])) {
                    $q->whereJsonContains('attributes->size', $filters['size']);
                }
                if (!empty($filters['color'])) {
                    $q->whereJsonContains('attributes->color', $filters['color']);
                }
            });
        }

        if (!empty($filters['rating'])) {
            $query->whereHas('reviews', function ($q) use ($filters) {
                $q->havingRaw('AVG(rating) >= ?', [$filters['rating']]);
            });
        }

        // Only apply sorting if explicitly specified, otherwise preserve original order
        if (!empty($filters['sort'])) {
            $sort = $filters['sort'];
            match ($sort) {
                'price_asc' => $query->orderBy('retail_price', 'asc'),
                'price_desc' => $query->orderBy('retail_price', 'desc'),
                'popular' => $query->orderByDesc(
                    Product::selectRaw('COUNT(*)')
                        ->from('order_items')
                        ->whereColumn('order_items.product_id', 'products.id')
                ),
                'rating' => $query->orderByDesc(
                    Product::selectRaw('COALESCE(AVG(rating), 0)')
                        ->from('reviews')
                        ->whereColumn('reviews.product_id', 'products.id')
                ),
                'latest' => $query->latest(),
                default => $query->orderBy('id', 'asc'),
            };
        } else {
            // Preserve original product order by ID ascending (when no sort is specified)
            $query->orderBy('id', 'asc');
        }

        return $query;
    }
}
