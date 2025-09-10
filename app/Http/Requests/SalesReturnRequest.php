<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SalesReturnRequest extends FormRequest
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
        'sales_invoice_id' => 'required|exists:sales_invoices,id',
        "note" => "nullable|string",
        'items' => 'required|array|min:1',
        'items.*.product_id' => 'required|exists:products,id',
        'items.*.quantity' => 'required|integer|min:1',
        'items.*.tax_amount' => 'nullable|numeric|min:0',
        'items.*.discount_amount' => 'nullable|numeric|min:0',
        'items.*.tax_id'=> 'nullable|exists:product_units,id',
        'sub_total' => 'required|numeric|min:0',
        'tax_amount' => 'required|numeric|min:0',
        'grand_total' => 'required|numeric|min:0',
        'discount_amount' => 'nullable|numeric|min:0',
        ];
    }
}
