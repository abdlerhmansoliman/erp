<?php 

namespace App\Repositories;

use App\Models\Product;
use App\Models\User;
use App\Repositories\Interfaces\ProductRepositoryInterface;

class ProducetRepository implements ProductRepositoryInterface
{
    public function getAllProducts()
    {
        return Product::with('category:id,name')->get([
    'id', 'name', 'price', 'description', 'quantity', 'sku', 'category_id'
        ]);;
    }
    public function getProductById($id)
    {
        return Product::with('category:id,name')->findOrFail($id, [
             'name', 'price', 'description', 'quantity', 'sku', 'category_id'
        ]);
    }
    public function createProduct(array $data)
    {
        return Product::create($data);
    }
    public function updateProduct($id, array $data)
    {
    $product=Product::findOrfail($id);
    return $product->update($data);
    }
    public function deleteProduct($id)
    {
        return Product::destroy($id);
    }
    public function searchProducts($query)
    {
        // Implementation for searching products
    }

}