<?php

namespace App\Helpers;

use App\Models\Discount;
use App\Models\Product;

class DiscountHelper
{
    /**
     * Get discount amount for a specific product.
     *
     * @param  float  $lineBase  // quantity * unit_price
     */
    public static function getProductDiscountAmount(int $productId, float $lineBase): float
    {
        $product = Product::with('discounts')->find($productId);
        if (! $product || $product->discounts->isEmpty()) {
            return 0.0;
        }

        // Get the first discount from the loaded relationship
        $discount = $product->discounts->first();
        if (! $discount) {
            return 0.0;
        }

        return self::calculateDiscount($lineBase, $discount);
    }

    /**
     * Get invoice-level discount amount.
     */
    public static function getInvoiceDiscountAmount(float $subtotal, ?int $discountId = null): float
    {
        if (! $discountId) {
            return 0.0;
        }

        $discount = Discount::find($discountId);
        if (! $discount) {
            return 0.0;
        }

        return self::calculateDiscount($subtotal, $discount);
    }

    /**
     * Shared discount calculation logic.
     */
    protected static function calculateDiscount(float $amount, Discount $discount): float
    {
        // Now we can use direct model attributes
        $value = $discount->value ?? 0;
        $isPercentage = $discount->is_percentage ?? false;

        if ($isPercentage) {
            return round($amount * ($value / 100), 2);
        }

        return min($value, $amount);
    }
}
