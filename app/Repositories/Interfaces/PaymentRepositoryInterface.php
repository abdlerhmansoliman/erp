<?php

namespace App\Repositories\Interfaces;

interface PaymentRepositoryInterface
{
    public function create(array $data);
    public function getByPayable(string $type, int $id) ;
    public function getTotalPaid(string $type, int $id);
}
