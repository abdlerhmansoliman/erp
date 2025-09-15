<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WarehouseShowResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
public function toArray($request)
{
    $response = [
        'warehouse' => [
            'id' => $this->id,
            'name' => $this->name,
            'address' => $this->address,
            'email' => $this->email,
            'phone' => $this->phone,
        ]
    ];

    // If products data exists, merge it
    if (isset($this->products_data)) {
        // Don't merge warehouse data, just merge pagination data
        $paginationData = $this->products_data;
        unset($paginationData['warehouse']); // Remove warehouse to avoid overwrite
        
        $response = array_merge($response, $paginationData);
        
        // Add counts to our existing warehouse data
        $response['warehouse']['product_count'] = $this->products_data['warehouse']['product_count'];
        $response['warehouse']['total_quantity'] = $this->products_data['warehouse']['total_quantity'];
    }

    return $response;
}
}
