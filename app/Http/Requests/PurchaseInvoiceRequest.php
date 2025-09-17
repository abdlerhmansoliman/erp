<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseInvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
  return [
    'supplier_id' => 'required|exists:suppliers,id',
    'warehouse_id' => 'required|exists:warehouses,id',
    'status' => 'nullable|string|in:draft,ordered,received,cancelled',
    'sub_total' => 'required|numeric|min:0',
    'discount_amount' => 'nullable|numeric|min:0',
    'tax_amount' => 'nullable|numeric|min:0',
    'grand_total' => 'required|numeric|min:0',
    'invoice_number' => 'sometimes|string|max:255|unique:purchase_invoices,invoice_number',
    'items' => 'required|array|min:1',
    'items.*.product_id' => 'required|exists:products,id',
    'items.*.quantity' => 'required|numeric|min:1',
    'items.*.unit_price' => 'required|numeric|min:0',
    'items.*.discount_amount' => 'nullable|numeric|min:0',
    'items.*.tax_id' => 'nullable|exists:taxes,id',
    'items.*.tax_amount' => 'nullable|numeric|min:0',
    'items.*.total_price' => 'required|numeric|min:0',
    'items.*.net_price' => 'nullable|numeric|min:0',
    'payment_status' => 'nullable|string|in:paid,due,partial',
    'due_date' => 'nullable|date|after_or_equal:today',
    'shipping_cost' => 'nullable|numeric|min:0',
    'paid_amount' => 'required_if:payment_status,partial|numeric|min:0',
    'payment_date' => 'required_if:payment_status,paid,partial|date',
    ];
    }
}
