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
        'invoice_number' => $this->invoice_number, // ده هيظهر بالشكل INV-000{id}
        'supplier_id' => $this->supplier_id,
        'status' => $this->status,
        'total_amount' => $this->total_amount,
        'created_at' => $this->created_at,
        'updated_at' => $this->updated_at,
        
        'supplier' => new SupplierResource($this->whenLoaded('supplier')),
    ];    }
}
