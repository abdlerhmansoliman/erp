<?php 

namespace App\Repositories;

use App\Models\PurchaseInvoice;
use App\Models\PurchaseItems;
use App\Models\User;
use App\Repositories\Interfaces\PurchaseInvoiceRepositoryInterface;

class PurchaseInvoiceRepository implements PurchaseInvoiceRepositoryInterface
{
    public function all(array $filters)
    {
        return PurchaseInvoice::query()
        ->when($filters['search']??null, function ($q,$search) {
            return $q->where('invoice_number', 'like', "%{$search}%")
            ->orWhereHas('supplier', function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            });
        })
        ->orderBy($filters['sortBy']??'id', $filters['sortDir']??'desc')
        ->paginate($filters['per_page'] ?? 10);
    }
    public function findById($id)
    {
    return PurchaseInvoice::with('items')->find($id);
    }
    public function create(array $data)
    {
        return PurchaseInvoice::create($data);
    }
    public function update($id, array $data)
    {
    $invoice = $this->findById($id);
    $invoice->update($data);
    return $invoice;
    }
    public function delete($id)
    {
        $invoice = $this->findById($id);
        return PurchaseInvoice::destroy($id);
    }
    public function addITems($productId, array $items){
        foreach ($items as $item){
            $item['purchase_invoice_id'] = $productId;
            $item['total_price'] = $item['quantity'] * $item['unit_price'];
            PurchaseItems::create($item);
        }
    }
    public function deleteItems($productId)
    {
        return PurchaseItems::where('purchase_invoice_id', $productId)->delete();
    }
}