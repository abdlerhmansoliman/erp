<?php

namespace App\Services;

use App\Helpers\TaxHelper;
use App\Models\InvoiceItem;
use App\Models\PurchaseItems;
use App\Models\Stock;
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

        $purchase = $this->purchaseInvoiceRepository->create([
            'supplier_id' => $data['supplier_id'],
            'status' => $data['status'] ?? 'pending',
            'sub_total' => 0,
            'tax_amount' => 0,
            'grand_total' => 0,
            'invoice_number' => $data['invoice_number'] ?? null,
        ]);

        $subTotal = 0; 
        $itemsProductTax = 0;

        foreach ($data['items'] as $item) {
            $quantity = (int) $item['quantity'];
            $unitPrice = (float) $item['unit_price'];
            $warehouseId = $item['warehouse_id'];
            $productId  = (int) $item['product_id'];
            // حساب سعر السطر قبل الضريبة
            $lineBase = $quantity * $unitPrice;
            $subTotal += $lineBase;
            // حساب ضريبة المنتج إذا موجودة
            $productTaxRate = TaxHelper::productTaxRate($productId);
            $productTaxAmount = round($lineBase * ($productTaxRate / 100), 2);
            $itemsProductTax += $productTaxAmount;
            dd($productTaxRate, $productTaxAmount, $lineBase, $subTotal, $itemsProductTax);
            // تحديث المخزون
            $stock = Stock::firstOrCreate(
                ['product_id' => $productId, 'warehouse_id' => $warehouseId],
                ['quantity' => 0]
            );
            $stock->quantity += $quantity;
            $stock->save();

            // حفظ البنود في الجدول
            PurchaseItems::create([
                'purchase_invoice_id' => $purchase->id,
                'product_id' => $productId,
                'warehouse_id' => $warehouseId,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'tax_amount' => $productTaxAmount,
                'tax_rate' => $productTaxRate,
                'total_price' => $lineBase,
                'net_price' => $lineBase + $productTaxAmount,
            ]);
        }

        // حساب ضريبة الفاتورة العامة
        $invoiceTaxRate = TaxHelper::invoiceTaxRate();    
        $invoiceTaxAmount = round($subTotal * ($invoiceTaxRate / 100), 2);

        // المجموع النهائي
        $grandTotal = $subTotal + $itemsProductTax + $invoiceTaxAmount;
        // --- IGNORE ---
        // تحديث الفاتورة
        $purchase->update([
            'sub_total' => $subTotal,
            'tax_amount' => $itemsProductTax + $invoiceTaxAmount,
            'grand_total' => $grandTotal,
        ]);
        return $purchase;
    });
}


    public function updateInvoice($id, array $data)
    {
         return DB::transaction(function () use ($id, $data) {
            $items = $data['items'] ?? null;
            unset($data['items']);
                $invoice = $this->purchaseInvoiceRepository->update($id, $data);
            if ($items) {
                $oldItems=PurchaseItems::where('purchase_invoice_id', $id)->get();
                foreach ($oldItems as $oldItem) {
                    $stok=Stock::where('product_id',$oldItem->product_id)
                        ->where('warehouse_id', $oldItem->warehouse_id)
                        ->first();
                        if($stok){
                            $stok->quantity -= $oldItem->quantity;
                            $stok->save();
                        }
                }
                PurchaseItems::where('purchase_invoice_id', $id)->delete();
                $total = 0;
                foreach($items as $item){
                    $quantity=$item['quantity'];
                    $unitPrice=$item['unit_price'];
                    $warehouseId=$item['warehouse_id'];
                    
                    $total+= $quantity * $unitPrice;

                    $stok=Stock::firstOrCreate(
                    ['product_id' => $item['product_id'], 
                            'warehouse_id' => $warehouseId],
                    ['quantity' => 0]
                    );
                    $stok->quantity += $quantity;
                    $stok->save();
                    PurchaseItems::create([
                        'purchase_invoice_id' => $invoice->id,
                        'product_id' => $item['product_id'],
                        'warehouse_id' => $warehouseId,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'total_price' => $quantity * $unitPrice,
                    ]);
                    
                }
                $invoice->update([
                    'total_amount' => $total,
                ]);
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
