<?php

namespace App\Services;

use App\Models\InvoiceItem;
use App\Repositories\Interfaces\PurchaseInvoiceRepositoryInterface;
use Illuminate\Support\Facades\DB;

class PurchaseInvoiceService
{
    /**
     * Create a new class instance.
     */
  protected $purchaseInvoiceRepository;
    protected InvoiecItemService $invoiceItemService;

    public function __construct( purchaseInvoiceRepositoryInterface $purchaseInvoiceRepository, InvoiecItemService $invoiceItemService)
     {
        $this->purchaseInvoiceRepository = $purchaseInvoiceRepository;
        $this->invoiceItemService = $invoiceItemService;
    }
    public function getAllInvoices(array $filters )
    {
        return $this->purchaseInvoiceRepository->all($filters);
    }
    public function getInvoiceById($id)
    {
        return $this->purchaseInvoiceRepository->findById($id);
    }
  public function createInvoice(array $data)
{
    return DB::transaction(function() use ($data) {

        // 1️⃣ إنشاء الفاتورة
        $purchase = $this->purchaseInvoiceRepository->create([
            'supplier_id' => $data['supplier_id'],
            'status' => $data['status'] ?? 'pending',
            'total_amount' => 0
        ]);

        $total = 0;

        foreach ($data['items'] as $item) {
            $quantity = $item['quantity'];
            $unitPrice = $item['unit_price'];
            $warehouseId = $item['warehouse_id'];

            $total += $quantity * $unitPrice;

            $stock = \App\Models\Stock::firstOrCreate(
                ['product_id' => $item['product_id'], 'warehouse_id' => $warehouseId],
                ['quantity' => 0]
            );
            $stock->quantity += $quantity;
            $stock->save();

            \App\Models\PurchaseItems::create([
                'purchase_invoice_id' => $purchase->id,
                'product_id' => $item['product_id'],
                'warehouse_id' => $warehouseId,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_price' => $quantity * $unitPrice,
            ]);
        }

        $purchase->update([
            'total_amount' => $total,
        ]);

        return $purchase;
    });
}

    public function updateInvoice($id, array $data)
    {
         return DB::transaction(function () use ($id, $data) {
            $items = $data['items'] ?? null;
            unset($data['items']);

            if ($items) {
                $total = $this->invoiceItemService->calculateTotal($items);
                $data['total_price'] = $total;
            }

                $invoice = $this->purchaseInvoiceRepository->update($id, $data);
            if ($items) {
                $this->invoiceItemService->deleteItemsForInvoice($invoice);

                foreach ($items as $item) {
                    $this->invoiceItemService->createItemForInvoice($invoice, $item);
                }
            }

            return $invoice->load('supplier', 'items.product');
        });

    }

    public function deletePurchase($id)
    {
        return DB::transaction(function () use ($id) {
            $this->purchaseInvoiceRepository->deleteItems($id);
            return $this->purchaseInvoiceRepository->delete($id);
        });
    }
}
