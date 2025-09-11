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
        'id' => $this->id,
        'sales_invoice_id' => $this->sales_invoice_id,
        'invoice_number' => $this->invoice_number, 
        'status' => $this->status,
        'original_invoice_number' => $this->invoice?->invoice_number,
        'return_date' => $this->return_date ? \Carbon\Carbon::parse($this->return_date)->format('Y-m-d') : null,
        'sub_total' => $this->sub_total,
        'tax_amount' => $this->tax_amount,
        'discount_amount' => $this->discount_amount,
        'grand_total' => $this->grand_total,
        'note' => $this->note,
        'created_at' => $this->created_at?->format('Y-m-d H:i'),
        'updated_at' => $this->updated_at,

        // العميل والمخزن من العلاقة المباشرة
        'customer' => $this->customer ? [
            'id' => $this->customer->id,
            'name' => $this->customer->name,
            'email' => $this->customer->email,
            'phone' => $this->customer->phone,
            'address' => $this->customer->address,
        ] : null,

        'warehouse' => $this->warehouse ? [
            'id' => $this->warehouse->id,
            'name' => $this->warehouse->name,
        ] : null,

        // العناصر المرتجعة
        'items' => SalesReturnItemResource::collection($this->items),
    ];
}

}
