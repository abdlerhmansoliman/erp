<?php 

namespace App\Repositories;

use App\Models\Product;
use App\Models\User;
use App\Repositories\Interfaces\ProductRepositoryInterface;

class ProducetRepository implements ProductRepositoryInterface
{
    public function getAllProducts()
    {
        return Product::select('id', 'name', 'price', 'description','sku', 'category_id', 'unit_id','purchase_price')->get();
    }
    public function getProductById($id)
    {
        return Product::select('id', 'name', 'price', 'description','sku', 'category_id', 'unit_id','purchase_price')->findOrFail($id);
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