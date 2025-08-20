<?php
namespace App\Repositories\Interfaces;

interface SalesItemRepositoryInterface
{
    public function deleteByInvoice(int $invoiceId);
    public function getByInvoice(int $invoiceId);
    public function create(array $data);
    public function bulkInsert(array $rows);


}