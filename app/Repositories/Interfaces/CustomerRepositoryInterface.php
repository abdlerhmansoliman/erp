<?php
namespace App\Repositories\Interfaces;

use App\Models\User;
interface CustomerRepositoryInterface
{
    public function getAllCustomers();

    public function findById(int $id);

    public function create(array $data);

    public function delete(int $id): bool;

    public function update(int $id, array $data);


}