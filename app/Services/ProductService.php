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
    $products = $this->ProductRepository->getAllProducts($filters);
    // Eager load tax relationships
    if ($products->getCollection()) {
        $products->load('tax');
    }
    return $products;
   }
   
   public function getProductById($id){
    return $this->ProductRepository->getProductById($id)->load('tax');
   }

    public function createProduct(array $data){
    $data['product_code'] = strtoupper(uniqid('PROD_'));
    // Create product with tax relationship
    return $this->ProductRepository->createProduct($data)->load('tax');
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
        try {
            // Use the Product model directly for search since we can't find the repository
            $products = \App\Models\Product::where('name', 'like', "%{$query}%")
                ->orWhere('product_code', 'like', "%{$query}%")
                ->with(['tax', 'category', 'unit']) // Eager load relationships
                ->take(10) // Limit results
                ->get();
            
            return $products;
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
