<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Http\Resources\ProductResource;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $productService;
    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }
    public function index(Request $request){
    $filters = $request->only(['search', 'sortBy', 'sortDirection', 'perPage', 'page']);
    $products = $this->productService->getAllProducts($filters);

    return response()->json([
        'status' => 'success',
        'data'   => ProductResource::collection($products)
    ]);
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
    public function deleteMultiple(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids) || !is_array($ids)) {
            return response()->json([
                'status' => 'error',
                'message' => 'No valid IDs provided'
            ], 400);
        }

        $deletedCount = $this->productService->deleteMultipleProducts($ids);

        return response()->json([
            'status' => 'success',
            'message' => "$deletedCount products deleted successfully"
        ]);
    }

    public function search(Request $request)
    {
        try {
            $query = $request->input('q');
            
            if (empty($query)) {
                return response()->json([
                    'status' => 'success',
                    'data' => []
                ]);
            }

            $products = $this->productService->searchProducts($query);
            
            return response()->json([
                'status' => 'success',
                'data' => ProductResource::collection($products)
            ]);
            
        } catch (\Exception $e) {
            
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while searching for products'
            ], 500);
        }
    }

    /**
     * Get product's tax information
     */
    public function getTax($id)
    {
        $product = $this->productService->getProductById($id);
        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'tax' => $product->tax
            ]
        ]);
    }}
