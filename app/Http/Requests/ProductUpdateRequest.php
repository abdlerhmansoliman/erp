<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductUpdateRequest extends FormRequest
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
            'name' => 'sometimes|string|max:255',
            'price' => 'sometimes|numeric',
            'description' => 'sometimes|string',
            'quantity' => 'sometimes|integer',
            'sku' => 'sometimes|string|max:100|unique:products,sku',
            'category_id' => 'sometimes|exists:categories,id',
            'purchase_price' => 'sometimes|numeric',
            'sale_price' => 'sometimes|numeric',
        ];
    }
}
