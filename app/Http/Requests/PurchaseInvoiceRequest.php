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
        'items' => 'required|array|min:1',
        'status' => 'nullable|string|in:draft,ordered,received',
        'total_amount' => 'nullable|numeric|min:0',
        'grand_total' => 'nullable|numeric|min:0',
        'sub_total' => 'nullable|numeric|min:0',
        'tax_amount' => 'nullable|numeric|min:0',
    ];
    }
}
