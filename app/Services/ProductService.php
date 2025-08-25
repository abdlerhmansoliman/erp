<?php

namespace App\Services;

use App\Models\Unit;
use App\Repositories\Interfaces\AuthRepositoryInterface;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Support\Facades\Hash;

class ProductService
{
    /**
     * Create a new class instance.
     */
    protected $ProductRepository;
    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->ProductRepository = $productRepository;
    }

   public function getAllProducts(array $filters){
    return $this->ProductRepository->getAllProducts($filters);
   }
   public function getProductById($id){
    return $this->ProductRepository->getProductById($id);
   }
    public function createProduct(array $data){
    $data['product_code'] = strtoupper(uniqid('PROD_'));
     return $this->ProductRepository->createProduct($data);
    }
    public function updateProduct($id, array $data){
        return $this->ProductRepository->updateProduct($id, $data);
    }
    public function deleteProduct($id){
        return $this->ProductRepository->deleteProduct($id);
    }
    public function deleteMultipleProducts(array $ids){
        return $this->ProductRepository->deleteMultipleProducts($ids);
    }
    public function searchProducts($query)
    {
        return $this->ProductRepository->search($query);
    }
}
