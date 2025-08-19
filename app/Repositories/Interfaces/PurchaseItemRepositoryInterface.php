<?php
namespace App\Repositories\Interfaces;

interface PurchaseItemRepositoryInterface
{
    public function deleteByInvoice(int $invoiceId);
    public function getByInvoice(int $invoiceId);
    public function create(array $data);
    public function bulkInsert(array $rows);


}