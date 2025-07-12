<?php 

namespace App\Repositories;

use App\Models\PurchaseInvoice;
use App\Models\User;
use App\Repositories\Interfaces\PurchaseInvoiceRepositoryInterface;

class PurchaseInvoiceRepository implements PurchaseInvoiceRepositoryInterface
{
    public function all()
    {
        return PurchaseInvoice::with('supplier','items')->get();
    }
    public function findById($id)
    {
    return PurchaseInvoice::with('items.product', 'supplier')->findOrFail($id);
    }
    public function create(array $data)
    {
        return PurchaseInvoice::create($data);
    }
    public function update($id, array $data)
    {
    $invoice = PurchaseInvoice::findOrFail($id);
    $invoice->update($data);
    return $invoice->fresh()->load('items.product', 'supplier');
    }
    public function delete($id)
    {
        return PurchaseInvoice::destroy($id);
    }
}