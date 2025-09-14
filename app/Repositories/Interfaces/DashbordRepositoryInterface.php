<?php

namespace App\Repositories\Interfaces;

interface DashbordRepositoryInterface
{
public function getStats();
public function getLast7Days(string $model);
public function getRecntlySales();
public function getRecntlyPurchases();

}
