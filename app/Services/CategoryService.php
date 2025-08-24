<?php


namespace App\Services;

use App\Repositories\CategoryRepository;

class CategoryService
{
    protected $category;

    public function __construct(CategoryRepository $category)
    {
        $this->category = $category;
    }

    public function getAllCategories()
    {
        return $this->category->all();
    }

    public function getUnitById($id)
    {
        return $this->category->find($id);
    }

    public function createCategory($data)
    {
        return $this->category->create($data);
    }

    public function updateCategory($id, $data)
    {
        return $this->category->update($id, $data);
    }

    public function deleteCategory($id)
    {
        return $this->category->delete($id);
    }
}
