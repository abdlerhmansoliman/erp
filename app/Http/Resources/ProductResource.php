<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'product_code' => $this->product_code,
            'purchase_price' => $this->purchase_price,
            'category_id' => $this->category_id,
            'unit_id' => $this->unit_id,
            'tax_id' => $this->tax_id,
            'category' => [
                'id' => $this->category?->id,
                'name_ar' => $this->category?->name_ar,
                'name_en' => $this->category?->name_en,
            ],
            'unit_name' => $this->unit?->name,
            'tax' => $this->whenLoaded('tax', function() {
                return [
                    'id' => $this->tax->id,
                    'name' => $this->tax->name,
                    'rate' => $this->tax->rate
                ];
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
        }
}
