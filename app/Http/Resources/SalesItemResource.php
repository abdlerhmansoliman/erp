<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SalesItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
             'id'            => $this->id,
            'product_name'  => $this->product?->name,
            'quantity'      => $this->quantity,
            'unit_price'    => $this->unit_price,
            'discount'      => $this->discount_amount,
            'tax_amount'    => $this->tax_amount,
            'net_price'     => $this->net_price,
            'total_price'   => $this->total_price,
        ];
    }
}
