<?php

namespace App\Services;

use App\Models\SalesReturn;
use App\Repositories\SalesInvoiceRepository;
use App\Repositories\SalesRetrunItemRepository;
use App\Repositories\SalesRetrunRepository;
use Illuminate\Support\Facades\DB;

class SalesRetrunService
{
    public function __construct(protected SalesRetrunRepository $retrunRepo, 
    protected StockService $stockService , 
    protected SalesInvoiceRepository $invoiceRepo,
    protected SalesRetrunItemRepository $itemRepo
    ){}
    public function getAllReturns(array $filters){
        return $this->retrunRepo->all($filters);
    }

    public function show($id){
        $invoice= $this->retrunRepo->findByIdWithItems($id);
                foreach($invoice->items as $item){
            $returnedQty = $this->retrunRepo->sumReturnedQuantity($invoice->id, $item->product_id);
            $item->returned_quantity=$returnedQty;
            $item->available_quantity=$item->quantity-$returnedQty;
        }
          return $invoice;
    }
    public function create(array $data){
        return DB::transaction(function () use ($data) {
                $invoice=$this->invoiceRepo->findByIdWithItems($data['sales_invoice_id']);
                $return=$this->retrunRepo->create([
                    'sales_invoice_id'=>$invoice->id,
                    'customer_id'=>$invoice->customer_id,
                    'warehouse_id'=>$invoice->warehouse_id,
                    'status'=>'draft',
                    'return_date'=>now(),
                    'sub_total'=>$data['sub_total']??0,
                    'tax_amount'=>$data['tax_amount']??0,
                    'discount_amount'=>$data['discount_amount']??0,
                    'grand_total'=>$data['grand_total']??0,
                    'note'=>$data['note']??null
                ]);
                foreach($data['items']as $item){

                    $invoiceItem=$invoice->items->firstWhere('product_id', $item['product_id']);
                        
                    if(! $invoiceItem || $item['quantity']>$invoiceItem->quantity){
                        throw new \Exception("Quantity exceeds original invoice for product ID {$item['product_id']}");
                    }
                    $alreadyRetunedQty=$this->retrunRepo->sumReturnedQuantity(
                        $invoice->id,
                        $item['product_id']
                    );
                    $availableQty=$invoiceItem->quantity-$alreadyRetunedQty;
                    if($item['quantity']>$availableQty){
                        throw new \Exception("Return quantity exceeds available quantity for product ID {$item['product_id']}. Available: {$availableQty}");
                    }
                    
                    $unitPrice  = $invoiceItem->unit_price;
                        $totalPrice = $unitPrice * $item['quantity'];
                    $this->itemRepo->createItem([
                        'sales_return_id'   => $return->id,
                        'product_id'         => $item['product_id'],
                        'quantity'           => $item['quantity'],
                        'unit_price'         => $unitPrice,
                        'total_price'        => $totalPrice,
                        'tax_amount'         => $item['tax_amount'] ?? 0,
                        'discount_amount'    => $item['discount_amount'] ?? 0,
                        'tax_id'             => $invoiceItem->tax_id,
                    ]);
                $unitCost = $unitPrice
                 + ($item['tax_amount'] - $item['discount_amount']) / $item['quantity'];
                    $this->stockService->create([
                        'product_id'      => (int) $item['product_id'],
                        'warehouse_id'    => $invoice->warehouse_id,
                        'product_unit_id' => $item['product_unit_id'] ?? null,
                        'qty'             => (int) $item['quantity'],
                        'remaining'       => (int) $item['quantity'],
                        'net_unit_price'  => $unitPrice,
                        'model_id'        => $return->id,
                        'model_type'      => SalesReturn::class,
                        'unit_coast'      => $unitCost
                    ]);
                }

                 return $this->retrunRepo->findByIdWithItems($return->id);

        });
    }
    public function prepareReturnData($id){
        $invoice=$this->invoiceRepo->findByIdWithItems($id);
        if(!$invoice){
            return null;
        }
        foreach($invoice->items as $item){
            $returnedQty = $this->retrunRepo->sumReturnedQuantity($invoice->id, $item->product_id);
            $item->returned_quantity=$returnedQty;
            $item->available_quantity=$item->quantity-$returnedQty;
        }
        return $invoice;
    }
}