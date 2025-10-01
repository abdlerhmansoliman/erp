<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WarehouseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'address' => $this->address,
            'email' => $this->email,
            'phone' => $this->phone,
            'product_count'=>$this->product_count ?? 0,
            'total_quantity'=>$this->total_quantity?? 0
        ];
    }
}
