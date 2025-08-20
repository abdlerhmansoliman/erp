<?php

namespace App\Services\Billing;

use App\Helpers\DiscountHelper;
use App\Helpers\TaxHelper;

class InvoiceCalculator
{
    /**
     * Compute the line item amounts for the given product.
     *
     * @param int $productId id of the product to compute the line for
     * @param int $qty quantity of the product
     * @param float $unitPrice unit price of the product
     *
     * @return array with keys 'line_base', 'discount_amount', 'line_after_discount', 'tax_amount', 'net_price'
     */
        public static function computeLine(int $productId, int $qty, float $unitPrice, string $context): array
        {
            $lineBase = $qty * $unitPrice;               
            $discountAmount = DiscountHelper::getProductDiscountAmount($productId, $lineBase);
            $lineAfterDiscount = $lineBase - $discountAmount;        

            $productTaxRate = TaxHelper::productTaxRate($productId, $context);
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

    /**
     * Computes the invoice totals.
     *
     * @param float $subTotalAfterProductDiscount subtotal after product discounts
     * @param float $itemsProductTax total tax for all items
     * @param int|null $invoiceDiscountId discount id for invoice discount
     *
     * @return array with keys 'invoice_discount_amount', 'invoice_tax_amount', 'grand_total'
     */
            public static function computeInvoiceTotals(float $subTotalAfterProductDiscount, float $itemsProductTax, ?int $invoiceDiscountId, string $context = 'sales'): array
            {
                $invoiceDiscountAmount = DiscountHelper::getInvoiceDiscountAmount($subTotalAfterProductDiscount, $invoiceDiscountId ?? null);
                $subTotalAfterInvoiceDisc = $subTotalAfterProductDiscount - $invoiceDiscountAmount;
                $invoiceTaxRate = TaxHelper::invoiceTaxRate($context);
                $invoiceTaxAmount = round($subTotalAfterInvoiceDisc * ($invoiceTaxRate / 100), 2);
                
                $grandTotal = $subTotalAfterInvoiceDisc + $itemsProductTax + $invoiceTaxAmount;

                return [
                    'invoice_discount_amount' => $invoiceDiscountAmount,
                    'invoice_tax_amount' => $invoiceTaxAmount,
                    'grand_total' => $grandTotal,
                ];
            }
}
