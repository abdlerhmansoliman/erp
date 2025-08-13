<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StockUpdateRequest extends FormRequest
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
            'product_id' => 'sometimes|exists:products,id',
            'warehouse_id' => 'sometimes|exists:warehouses,id',
            'quantity' => 'sometimes|integer|min:0',
        ];
    }
}
