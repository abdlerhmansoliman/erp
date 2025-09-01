<?php 

namespace App\Repositories;

use App\Models\SalesInvoice;
use App\Models\SalesItem;
use App\Models\User;
use App\Repositories\Interfaces\SalesInvoiceRepositoryInterface;

class SalesInvoiceRepository implements SalesInvoiceRepositoryInterface
{
    public function all()
    {
                return SalesInvoice::query()
        ->when($filters['search']??null, function ($q,$search) {
            return $q->where('invoice_number', 'like', "%{$search}%")
            ->orWhereHas('customer', function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            });
        })
        ->orderBy($filters['sortBy']??'id', $filters['sortDir']??'desc')
        ->paginate($filters['per_page'] ?? 10);
    }
    public function findById($id)
    {
        return SalesInvoice::with('items')->find($id);
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
    public function findByIdWithItems($id)
    {
        return SalesInvoice::with('items')->find($id);
    }
}