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
            ->with('supplier') 
            ->when($filters['search'] ?? null, function ($q, $search) {
                return $q->where('id', 'like', "%{$search}%")
                    ->orWhereHas('supplier', function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%");
                    });
            })
            ->orderBy($filters['sortBy'] ?? 'id', $filters['sortDir'] ?? 'desc')
            ->paginate($filters['perPage'] ?? 10);
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

    public function deleteItems($productId)
    {
        return PurchaseItems::where('purchase_invoice_id', $productId)->delete();
    }
    public function findWithItems(int $id): ?PurchaseInvoice
    {
        return PurchaseInvoice::with('items')->find($id);
    }
    public function findByIdWithItems($id)
    {
        return PurchaseInvoice::with('items.product', 'supplier')->findOrFail($id);
    }
}