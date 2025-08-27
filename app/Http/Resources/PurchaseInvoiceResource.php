<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseInvoiceResource extends JsonResource
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
    'invoice_number' => $this->invoice_number,
    'supplier_name' => $this->supplier?->name,
    'warehouse_name' => $this->warehouse?->name,
    'status' => $this->status,
    'grand_total' => $this->grand_total,
    'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i') : null, // تاريخ + ساعة
    'updated_at' => $this->updated_at,
    'items' => PurchaseItemResource::collection($this->items),

    ];   
 }
}
