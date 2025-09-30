<?php

namespace App\Services;

use App\Models\TransferItem;
use App\Repositories\ProducetRepository;
use App\Repositories\TransferRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransferService
{
        public function __construct(
        protected TransferRepository $transferRepo,
        protected StockService $stockService,
        protected ProducetRepository $productRepo
    ) {}

    public function getAllTransfers(array $filters)
    {
        return $this->transferRepo->all($filters);
    }
    public function find($id)
    {
        return $this->transferRepo->find($id);
    }
    public function create (array $data)
    {
        return DB::transaction((function () use ($data){
            $transfer=$this->transferRepo->create([
                'from_warehouse_id'=>$data['from_warehouse_id'],
                'to_warehouse_id'=>$data['to_warehouse_id'],
                'transfer_date'=>$data['transfer_date'],
                // 'created_by'=>Auth::user()->id
            ]);
            foreach ($data['items'] as $item){
                $productId = $item['product_id'];
                $qty = $item['quantity'];

                $allocations = $this->stockService->allocateFIFOStock(
                    $productId,
                    $data['from_warehouse_id'],
                    $qty,
                    
                );
                    foreach ($allocations as $allocation) {
                    $this->stockService->create([
                        'product_id'   => $productId,
                        'warehouse_id' => $data['to_warehouse_id'],
                        'qty'     => $allocation['quantity'],
                        'remaining'    => $allocation['quantity'],
                        'unit_coast'   => $allocation['cost'],
                        'net_unit_price' => $allocation['net_unit_price'],
                        'model_type'   => get_class($transfer),
                        'model_id'     => $transfer->id,
                    ]);
                   $dd= TransferItem::create([
                    'transfer_id' => $transfer->id,
                    'product_id'  => $productId,
                    'quantity'    => $qty,
                ]);
                }
            }
                return $transfer->load('items.product');

        }));
    }
}


