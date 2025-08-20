<?php
namespace App\Helpers;

use App\Models\Product;
use App\Models\Tax;

class TaxHelper
{
public static function productTaxRate($productId,string $context) {
    
        $product = Product::with(['taxes' => function($q) use ($context) {
            $q->where('type', 'product')
              ->whereIn('applies_to', [$context, 'both']);
        }])->find($productId);
         if (!$product) return 0;
        return $product->taxes->sum(fn($tax) => (float) $tax->rate);
    }


    public static function invoiceTaxRate(string $context): float
    {
        return Tax::where('type', 'invoice')
            ->whereIn('applies_to', [$context, 'both'])
            ->get()
            ->sum(fn($tax) => (float) $tax->rate);
    }

}