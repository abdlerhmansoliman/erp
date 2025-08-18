<?php
namespace App\Repositories\Interfaces;

interface PurchaseInvoiceRepositoryInterface
{
    public function all(array $filters);
    public function findById($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function deleteItems($productId);

}