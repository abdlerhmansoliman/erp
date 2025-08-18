<?php
namespace App\Helpers;


class InvoiceCalculatorHelper
{
    public static function calculateTotal(array $items): float
    {
        return array_reduce($items, function ($carry, $item) {
            return $carry + ($item['quantity'] * $item['price']);
        }, 0);
    }

    public static function calculateTax(float $total, float $rate): float
    {
        return $total * ($rate / 100);
    }

    public static function calculateGrandTotal(float $total, float $tax = 0, float $discount = 0): float
    {
        return ($total + $tax) - $discount;
    }
    

}