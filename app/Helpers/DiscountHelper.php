<?php

namespace App\Helpers;

use App\Models\Product;
use App\Models\Discount;

class DiscountHelper
{
    /**
     * Get discount amount for a specific product.
     *
     * @param int $productId
     * @param float $lineBase  // quantity * unit_price
     * @return float
     */
    public static function getProductDiscountAmount(int $productId, float $lineBase): float
    {
        $product = Product::with('discounts')->find($productId);
        if (!$product || $product->discounts->isEmpty()) {
            return 0.0;
        }
        
        // Get the first discount from the loaded relationship
        $discount = $product->discounts->first();
        if (!$discount) {
            return 0.0;
        }
        
        return self::calculateDiscount($lineBase, $discount);
    }

    /**
     * Get invoice-level discount amount.
     *
     * @param float $subtotal
     * @param int|null $discountId
     * @return float
     */
    public static function getInvoiceDiscountAmount(float $subtotal, ?int $discountId = null): float
    {
        if (!$discountId) {
            return 0.0;
        }

        $discount = Discount::find($discountId);
        if (!$discount) {
            return 0.0;
        }

        return self::calculateDiscount($subtotal, $discount);
    }

    /**
     * Shared discount calculation logic.
     *
     * @param float $amount
     * @param \App\Models\Discount $discount
     * @return float
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