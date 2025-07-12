<?php 
namespace App\Repositories;

use App\Models\Department;
use App\Models\Position;
use App\Models\User;
use App\Repositories\Interfaces\positionRepositoryInterface;

class PositionRepository implements positionRepositoryInterface
{
 
    public function all()
    {
        return Position::get();
    }

    public function find($id)
    {
        return Position::find($id);
    }

    public function create(array $data)
    {
        return Position::create($data);
    }
    public function update($id, array $data)
    {
        $position=$this->find($id);
        $position->update($data);
        return $position;
    }

    public function delete($id)
    {
        return Position::destroy($id);
    }


}


