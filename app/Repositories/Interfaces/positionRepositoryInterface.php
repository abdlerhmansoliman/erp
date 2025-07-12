<?php
namespace App\Repositories\Interfaces;

use App\Models\User;
interface positionRepositoryInterface
{
    public function all();
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);

}