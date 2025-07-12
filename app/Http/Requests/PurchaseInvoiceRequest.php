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
        'invoice_date' => 'required|date',
        'notes' => 'nullable|string',
        'supplier_id' => 'required|exists:suppliers,id',
        'items' => 'required|array|min:1',
        'items.*.product_id' => 'nullable|numeric', 
        'items.*.quantity' => 'required|numeric|min:1',
        'items.*.price' => 'required|numeric|min:0',
        'items.*.name' => 'nullable|string|max:255',
    ];
    }
}
