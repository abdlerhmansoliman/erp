<?php
namespace App\Repositories\Interfaces;

use App\Models\User;
interface ProductRepositoryInterface
{
    public function getAllProducts(array $filters );

    public function getProductById($id);

    public function createProduct(array $data);

    public function updateProduct($id, array $data);

    public function deleteProduct($id);

    public function deleteMultipleProducts(array $ids);
    public function findForLookup(?string $search, int $limit = 20);
}