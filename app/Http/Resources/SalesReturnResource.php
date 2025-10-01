<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SalesReturnResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'sales_invoice_id'    => $this->sales_invoice_id,
            'invoice_number'      => $this->invoice_number, // رقم المرتجع
            'status'              => $this->status,
            'return_date'         => $this->return_date ? \Carbon\Carbon::parse($this->return_date)->format('Y-m-d') : null,
            'sub_total'           => $this->sub_total,
            'tax_amount'          => $this->tax_amount,
            'discount_amount'     => $this->discount_amount,
            'grand_total'         => $this->grand_total,
            'note'                => $this->note,
            'created_at'          => $this->created_at?->format('Y-m-d H:i'),
            'updated_at'          => $this->updated_at?->format('Y-m-d H:i'),

            'original_invoice_id'     => $this->invoice?->id,
            'original_invoice_number' => $this->invoice?->invoice_number,
            'original_invoice_status' => $this->invoice?->status,
            'original_invoice_total'  => $this->invoice?->grand_total,

            'customer_id'      => $this->customer?->id,
            'customer_name'    => $this->customer?->name,
            'customer_email'   => $this->customer?->email,
            'customer_phone'   => $this->customer?->phone,
            'customer_address' => $this->customer?->address,

            'warehouse_id'   => $this->warehouse?->id,
            'warehouse_name' => $this->warehouse?->name,

            'items' => SalesReturnItemResource::collection($this->items),
        ];
    }
}
