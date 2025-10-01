<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseReturnResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'purchase_invoice_id' => $this->purchase_invoice_id,
            'invoice_number'    => $this->invoice_number, 
            'status'            => $this->status,
            'return_date'       => $this->return_date ? \Carbon\Carbon::parse($this->return_date)->format('Y-m-d') : null,
            'sub_total'         => $this->sub_total,
            'tax_amount'        => $this->tax_amount,
            'discount_amount'   => $this->discount_amount,
            'grand_total'       => $this->grand_total,
            'note'              => $this->note,
            'created_at'        => $this->created_at?->format('Y-m-d H:i'),
            'updated_at'        => $this->updated_at?->format('Y-m-d H:i'),

            'original_invoice_id'     => $this->invoice?->id,
            'original_invoice_number' => $this->invoice?->invoice_number,
            'original_invoice_status' => $this->invoice?->status,
            'original_invoice_total'  => $this->invoice?->grand_total,

            'supplier_id'    => $this->invoice?->supplier?->id,
            'supplier_name'  => $this->invoice?->supplier?->name,
            'supplier_email' => $this->invoice?->supplier?->email,
            'supplier_phone' => $this->invoice?->supplier?->phone,
            'supplier_address' => $this->invoice?->supplier?->address,

            'warehouse_id'   => $this->invoice?->warehouse?->id,
            'warehouse_name' => $this->invoice?->warehouse?->name,

            'items' => PurchaseReturnItemResource::collection($this->items),
        ];
    }
}
