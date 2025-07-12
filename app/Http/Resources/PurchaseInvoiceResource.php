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
        'invoice_date' => $this->invoice_date,
        'notes' => $this->notes,
        'supplier_id' => $this->supplier_id,
        'supplier' => new SupplierResource($this->whenLoaded('supplier')),
        'items' => InvoiceItemResource::collection($this->whenLoaded('items')),
        'created_at' => $this->created_at,
    ];    }
}
