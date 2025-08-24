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
    'category_name' => $this->category?->name,
    'unit_name' => $this->unit?->name,
    'created_at' => $this->created_at,
    'updated_at' => $this->updated_at,
        ];
        }
}
