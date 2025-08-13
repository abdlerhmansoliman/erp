<?php
namespace App\Repositories\Interfaces;

use App\Models\Stock;

interface StockRepositoryInterface
{
    public function getAllStocks(array $filters);
    public function findById(int $id);
    public function create(array $data);
    public function update(Stock $stock, array $data);
    public function delete(Stock $stock);
}