<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePurchaseRequest extends FormRequest
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
        'supplier_id' => 'sometimes|exists:suppliers,id',
        'items' => 'sometimes|array|min:1',
        'status' => 'nullable|string|in:draft,ordered,received',
        'grand_total' => 'nullable|numeric|min:0',
        'sub_total' => 'nullable|numeric|min:0',
        'tax_amount' => 'nullable|numeric|min:0',
    ];
    }
}
