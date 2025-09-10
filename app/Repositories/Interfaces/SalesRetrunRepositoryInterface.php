<?php

namespace App\Repositories\Interfaces;

interface SalesRetrunRepositoryInterface
{
    public function all(array $filters);
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
  
     public function findByIdWithItems($id);
}
