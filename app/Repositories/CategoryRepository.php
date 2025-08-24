<?php

namespace App\Repositories;

use App\Models\Category;
use App\Models\Unit;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Repositories\Interfaces\UnitRepositoryInterface;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function all()
    {
        return Category::all();
    }
    public function create(array $data)
    {
        return Category::create($data);
    }
    public function find($id)
    {
        return Category::find($id);
    }
    public function update($id, array $data)
    {
        return Category::find($id)->update($data);
    }
    public function delete($id)
    {
        return Category::find($id)->delete();
    }
    

}
