<?php

namespace App\Services;

use App\Models\DiscountTier;
use App\Models\Product;
use App\Models\User;

class DiscountEngine
{
    public static function calculate(Product $product, int $quantity, string $type = 'retail', ?string $size = null): array
    {
        $basePrice = (float) $product->retail_price;

        if ($size) {
            $variant = $product->variants()->where('attributes->size', $size)->first();
            if ($variant && $variant->price !== null) {
                $basePrice = (float) $variant->price;
            }
        }

        if ($type === 'wholesale' && $product->wholesale_price > 0) {
            $basePrice = (float) $product->wholesale_price;
        }

        $tier = self::findTier($product, $quantity);
        $discountRate = $tier ? ($tier->discount_percent / 100) : 0;
        $discountAmount = $basePrice * $discountRate * $quantity;
        $finalUnitPrice = $basePrice * (1 - $discountRate);
        $total = $finalUnitPrice * $quantity;

        return [
            'base_price' => $basePrice,
            'quantity' => $quantity,
            'tier' => $tier,
            'discount_rate' => $discountRate,
            'discount_amount' => $discountAmount,
            'unit_price' => round($finalUnitPrice, 2),
            'total' => round($total, 2),
        ];
    }

    public static function findTier(Product $product, int $quantity): ?DiscountTier
    {
        $productTier = $product->discountTiers()
            ->where('is_active', true)
            ->where('min_quantity', '<=', $quantity)
            ->where(function ($query) use ($quantity) {
                $query->whereNull('max_quantity')
                      ->orWhere('max_quantity', '>=', $quantity);
            })
            ->orderBy('discount_percent', 'desc')
            ->first();

        if ($productTier) {
            return $productTier;
        }

        return DiscountTier::where('business_id', $product->business_id)
            ->whereNull('product_id')
            ->where('is_active', true)
            ->where('min_quantity', '<=', $quantity)
            ->where(function ($query) use ($quantity) {
                $query->whereNull('max_quantity')
                      ->orWhere('max_quantity', '>=', $quantity);
            })
            ->orderBy('discount_percent', 'desc')
            ->first();
    }

    public static function validateMoq(Product $product, int $quantity, string $type = 'retail'): bool
    {
        if ($type === 'wholesale') {
            return $quantity >= $product->moq;
        }
        return $quantity >= 1;
    }

    public static function recalculateCart(array $items): array
    {
        $subtotal = 0;
        $discountTotal = 0;

        foreach ($items as &$item) {
            $product = $item['product'] ?? Product::find($item['product_id']);
            $quantity = $item['quantity'];
            $type = $item['type'] ?? 'retail';
            $size = $item['size'] ?? null;

            $calc = self::calculate($product, $quantity, $type, $size);
            $item['unit_price'] = $calc['unit_price'];
            $item['discount_amount'] = $calc['discount_amount'];
            $item['line_total'] = $calc['total'];

            $subtotal += $calc['base_price'] * $quantity;
            $discountTotal += $calc['discount_amount'];
        }

        return [
            'items' => $items,
            'subtotal' => round($subtotal, 2),
            'discount_total' => round($discountTotal, 2),
            'total' => round($subtotal - $discountTotal, 2),
        ];
    }
}
