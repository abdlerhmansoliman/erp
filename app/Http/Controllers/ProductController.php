<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $productService;
    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }
    public function index(){
        $products=$this->productService->getAllProducts();
        return response()->json(
            [
                'status' => 'success',
                'data' => $products
            ],
            200
        );
    }
    public function show($id){
        $product = $this->productService->getProductById($id);
        return response()->json(
            [
                'status' => 'success',
                'data' => $product
            ],
            200
        );
    }
    
    public function store(ProductStoreRequest $request){
        $data=$request->validated();
        $product=$this->productService->createProduct($data);
        return response()->json([
            'status' => 'success',
            'data' => $product
        ]);
    }
    public function update(ProductUpdateRequest $request, $id){
        $data=$request->validated();
        $product=$this->productService->updateProduct($id, $data);
        return response()->json([
            'status' => 'success',
            'data' => $product
        ]);
    }
    public function destroy($id){
        $this->productService->deleteProduct($id);
        return response()->json([
            'status' => 'success',
            'message' => 'Product deleted successfully'
        ]);
    }

}
