<?php

namespace App\Repositories\Interfaces;

interface TransferRepositoryInterface
{
    public function all();
    public function find($id);
    public function create(array $data);

}
