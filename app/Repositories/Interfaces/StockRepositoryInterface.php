<?php
namespace App\Repositories\Interfaces;

interface StockRepositoryInterface
{
    public function getAllStocks(array $filters);
    public function findById(int $id);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
}