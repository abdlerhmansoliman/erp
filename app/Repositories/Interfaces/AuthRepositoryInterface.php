<?php

namespace App\Repositories\Interfaces;

interface AuthRepositoryInterface
{
    public function findByEmail(string $email);

    public function create(array $data);

    public function delete(int $id);
}
