<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StockStoreRequest extends FormRequest
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
            'product_id' => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'qty' => 'required|integer|min:0',
            'product_unit_id' => 'required|exists:units,id',
            'model_type' => 'required|string',
            'model_id' => 'required|integer|min:1',
            'remaining' => 'required|integer|min:0',
            'net_unit_price' => 'required|numeric|min:0',
        ];
    }
}
