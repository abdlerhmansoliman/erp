<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryStoreRequest;
use App\Http\Requests\CategoryUpdateRequest;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(protected CategoryService $categoryService)
    {}
    public function index()
    {
        return response()->json([
            'status' => 'success',
            'data' => $this->categoryService->getAllCategories()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function store(CategoryStoreRequest $request)
    {
        $data = $request->validated();
        $category = $this->categoryService->createCategory($data);
        return response()->json([
            'success' => true,
            'data' => $category,
        ], 201);
    }


    public function update(CategoryUpdateRequest $request, string $id)
    {
        $data=$request->validated();
        $category=$this->categoryService->updateCategory($id, $data);
        return response()->json([
            'success' => true,
            'data' => $category,
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
