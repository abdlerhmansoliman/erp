<?php

namespace App\Services\Billing;

use App\Helpers\DiscountHelper;
use App\Helpers\TaxHelper;

class InvoiceCalculator
{
    public static function computeLine(int $productId, int $qty, float $unitPrice): array
    {
        $lineBase = $qty * $unitPrice;               
        $discountAmount = DiscountHelper::getProductDiscountAmount($productId, $lineBase);
        $lineAfterDiscount = $lineBase - $discountAmount;        
        $productTaxRate = TaxHelper::productTaxRate($productId);
        $productTaxAmount = round($lineAfterDiscount * ($productTaxRate / 100), 2);
        $netPrice = $lineAfterDiscount + $productTaxAmount;
        return [
            'line_base'=> $lineBase,
            'discount_amount'=> $discountAmount,
            'line_after_discount'=> $lineAfterDiscount,
            'tax_amount'=> $productTaxAmount,
            'net_price'=> $netPrice,
        ];
    }

    public static function computeInvoiceTotals(float $subTotalAfterProductDiscount, float $itemsProductTax, ?int $invoiceDiscountId): array
    {
        $invoiceDiscountAmount = DiscountHelper::getInvoiceDiscountAmount($subTotalAfterProductDiscount, $invoiceDiscountId ?? null);
        $subTotalAfterInvoiceDisc = $subTotalAfterProductDiscount - $invoiceDiscountAmount;

        $invoiceTaxRate = TaxHelper::invoiceTaxRate();
        $invoiceTaxAmount = round($subTotalAfterInvoiceDisc * ($invoiceTaxRate / 100), 2);

        $grandTotal = $subTotalAfterInvoiceDisc + $itemsProductTax + $invoiceTaxAmount;

        return [
            'invoice_discount_amount' => $invoiceDiscountAmount,
            'invoice_tax_amount' => $invoiceTaxAmount,
            'grand_total' => $grandTotal,
        ];
    }
}
