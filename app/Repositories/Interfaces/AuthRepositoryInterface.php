<?php
namespace App\Repositories\Interfaces;

use App\Models\User;
interface AuthRepositoryInterface
{
    public function findByEmail(string $email);
    public function create(array $data);
    public function delete(int $id);
}