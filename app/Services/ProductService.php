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

   public function getAllProducts(){
    return $this->ProductRepository->getAllProducts();
   }
   public function getProductById($id){
    return $this->ProductRepository->getProductById($id);
   }
    public function createProduct(array $data){
     return $this->ProductRepository->createProduct($data);
    }
    public function updateProduct($id, array $data){
        return $this->ProductRepository->updateProduct($id, $data);
    }
    public function deleteProduct($id){
        return $this->ProductRepository->deleteProduct($id);
    }
}
