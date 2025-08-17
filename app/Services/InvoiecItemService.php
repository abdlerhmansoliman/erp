<?php

namespace App\Services;

use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\PurchaseInvoice;
use App\Models\SalesInvoice;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;

class InvoiecItemService
{
 public function createItemForInvoice($invoice, array $itemData): InvoiceItem
{
    return DB::transaction(function () use ($invoice, $itemData) {
        $product = Product::findOrFail($itemData['product_id']);
        $quantity = $itemData['quantity'];
        $warehouseId = $itemData['warehouse_id'] ; 

        if ($invoice instanceof SalesInvoice) {
            $stock = Stock::where('product_id', $product->id)
                          ->where('warehouse_id', $warehouseId)
                          ->firstOrFail();

            if ($stock->quantity < $quantity) {
                throw new \Exception("Out of stock: {$product->name}");
            }

            $stock->quantity -= $quantity;
            $stock->save();
            $price = $product->price;

        } elseif ($invoice instanceof PurchaseInvoice) {
            $stock = Stock::firstOrCreate(
                ['product_id' => $product->id, 'warehouse_id' => $warehouseId],
                ['quantity' => 0]
            );

            $stock->quantity += $quantity;
            $stock->save();
            $price = $itemData['price'] ?? $product->purchase_price;
        }

        return InvoiceItem::create([
            'product_id' => $product->id,
            'quantity' => $quantity,
            'price' => $price,
            'total_price' => $quantity * $price,
            'invoiceable_type' => get_class($invoice),
            'invoiceable_id' => $invoice->id,
            
        ]);
    });
}

    public function calculateTotal(array $items): float
    {
        return collect($items)->sum(function ($item) {
            $product = Product::findOrFail($item['product_id']);
            return $item['quantity'] * $product->price;
        });
    }
    public function deleteItemsForInvoice($invoice): void
    {
    foreach ($invoice->items as $item) {
        $product = Product::findOrFail($item->product_id);

        if ($invoice instanceof PurchaseInvoice) {
            $product->decrement('quantity', $item->quantity);
        } elseif ($invoice instanceof SalesInvoice) {
            $product->increment('quantity', $item->quantity);
        }

        $item->delete();
    }
    }
 
}
