<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransferResource extends JsonResource
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
        'from_warehouse' => $this->fromWarehouse?->name,
        'to_warehouse'   => $this->toWarehouse?->name,
        'transfer_date'  => $this->transfer_date,
        'created_by' => optional($this->createdBy)->name,
        'status' => $this->status
    ];
    }
}
