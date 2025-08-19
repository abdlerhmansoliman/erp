<?php
namespace App\Helpers;

use App\Models\Product;
use App\Models\Tax;

class TaxHelper
{
public static function productTaxRate($productId) {
    $product = Product::with(['taxes' => function($q){
        $q->where('type','product');
    }])->find($productId);
    
    if (!$product) return 0;
return $product->taxes->sum(fn($tax) => (float) $tax->rate);

}


public static function invoiceTaxRate(){
    return Tax::where('type','invoice')->get()->sum(function($tax){
        return (float) $tax->rate;
    });
}

}