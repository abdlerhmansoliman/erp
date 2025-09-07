<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseReturnRequest extends FormRequest
{
    public function authorize(): bool
    {
        // هنا ممكن تحط شرط صلاحيات لو محتاج
        return true;
    }

    public function rules(): array
    {
        return [
            'purchase_invoice_id' => 'required|exists:purchase_invoices,id',
            "note" => "nullable|string",
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.total_price' => 'required|numeric|min:0',
            'sub_total' => 'required|numeric|min:0',
            'tax_amount' => 'required|numeric|min:0',
            'grand_total' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'purchase_invoice_id.required' => 'The purchase invoice is required.',
            'purchase_invoice_id.exists' => 'The selected purchase invoice does not exist.',
            'items.required' => 'At least one item is required.',
            'items.array' => 'Items must be an array.',
            'items.*.product_id.required' => 'Product is required for each item.',
            'items.*.product_id.exists' => 'Selected product does not exist.',
            'items.*.quantity.required' => 'Quantity is required for each item.',
            'items.*.quantity.integer' => 'Quantity must be an integer.',
            'items.*.quantity.min' => 'Quantity must be at least 1.',
            'items.*.total_price.required' => 'Total price is required for each item.',
            'items.*.total_price.numeric' => 'Total price must be a number.',
            'sub_total.required' => 'Sub total is required.',
            'sub_total.numeric' => 'Sub total must be a number.',
            'tax_amount.required' => 'Tax amount is required.',
            'tax_amount.numeric' => 'Tax amount must be a number.',
            'grand_total.required' => 'Grand total is required.',
            'grand_total.numeric' => 'Grand total must be a number.',
        ];
    }
}
