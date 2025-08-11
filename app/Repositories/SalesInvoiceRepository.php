<?php 

namespace App\Repositories;

use App\Models\SalesInvoice;
use App\Models\User;
use App\Repositories\Interfaces\SalesInvoiceRepositoryInterface;

class SalesInvoiceRepository implements SalesInvoiceRepositoryInterface
{
    public function all()
    {
        return SalesInvoice::all();
    }
    public function findById($id)
    {
        return SalesInvoice::with(['items.product', 'customer'])->findOrFail($id);
    }
    public function create(array $data)
    {
        return SalesInvoice::create($data);
    }
    public function update($id, array $data)
    {
        $invoice = SalesInvoice::findOrFail($id);
        $invoice->update($data);
        return $invoice->fresh(); 
    }
    public function delete($id)
    {
        return SalesInvoice::where('id', $id)->delete();
    }

}