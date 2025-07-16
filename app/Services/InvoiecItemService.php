<?php

namespace App\Services;

use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\PurchaseInvoice;
use App\Models\SalesInvoice;
use Illuminate\Support\Facades\DB;

class InvoiecItemService
{
  public function createItemForInvoice($invoice, array $itemData): InvoiceItem
    {
        return DB::transaction(function () use ($invoice, $itemData) {
            $product = Product::findOrFail($itemData['product_id']);
            $quantity = $itemData['quantity'];

            if ($invoice instanceof SalesInvoice) {
                if ($product->quantity < $quantity) {
                    throw new \Exception("out of stock{$product->name}");
                }

                $product->decrement('quantity', $quantity);
                $price = $product->price;
            } elseif ($invoice instanceof PurchaseInvoice) {
                $product->increment('quantity', $quantity);
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
