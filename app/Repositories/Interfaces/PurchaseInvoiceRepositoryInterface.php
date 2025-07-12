<?php
namespace App\Repositories\Interfaces;

use App\Models\User;
interface PurchaseInvoiceRepositoryInterface
{
    public function all();
    public function findById($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
}