<?php

namespace App\Http\Resources;

use Carbon\Carbon;
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
    'payment_status' => $this->payment_status,
    'shipping_cost' => $this->shipping_cost,
    'sub_total' => $this->sub_total,
    'tax_amount' => $this->tax_amount,
    'discount_amount' => $this->discount_amount,
    'due_date' => $this->due_date ? Carbon::parse($this->due_date)->format('Y-m-d') : null,
    'items' => PurchaseItemResource::collection($this->items),

    ];   
 }
}
