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
            'id' => $this->id,
            'purchase_invoice_id' => $this->purchase_invoice_id,
            'invoice_number' => $this->invoice_number, // رقم المرتجع نفسه
            'status' => $this->status,
            'return_date' => $this->return_date ? \Carbon\Carbon::parse($this->return_date)->format('Y-m-d') : null,
            'sub_total' => $this->sub_total,
            'tax_amount' => $this->tax_amount,
            'discount_amount' => $this->discount_amount,
            'grand_total' => $this->grand_total,
            'note' => $this->note,
            'created_at' => $this->created_at?->format('Y-m-d H:i'),
            'updated_at' => $this->updated_at,
            
            // الفاتورة الأصلية
            'invoice' => $this->invoice ? [
                'id' => $this->invoice->id,
                'invoice_number' => $this->invoice->invoice_number,
                'supplier_id' => $this->invoice->supplier_id,
                'warehouse' => optional($this->warehouse) ? [
                    'id' => $this->warehouse->id,
                    'name' => $this->warehouse->name,
                ] : null,                
                'status' => $this->invoice->status,
                'sub_total' => $this->invoice->sub_total,
                'tax_amount' => $this->invoice->tax_amount,
                'discount_amount' => $this->invoice->discount_amount,
                'grand_total' => $this->invoice->grand_total,
                'created_at' => $this->invoice->created_at?->format('Y-m-d H:i'),
                'supplier' => $this->invoice->supplier ? [
                    'id' => $this->invoice->supplier->id,
                    'name' => $this->invoice->supplier->name,
                    'email' => $this->invoice->supplier->email,
                    'phone' => $this->invoice->supplier->phone,
                    'address' => $this->invoice->supplier->address,
                ] : null,
            ] : null,

            // العناصر المرتجعة
            'items' => PurchaseReturnItemResource::collection($this->items),
        ];
    }
}
