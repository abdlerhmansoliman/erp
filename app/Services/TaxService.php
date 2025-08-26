<?php

namespace App\Services;

use App\Repositories\TaxRepository;

class TaxService
{
    protected $taxRepo;

    public function __construct(TaxRepository $taxRepo)
    {
        $this->taxRepo = $taxRepo;
    }

    public function getAllTaxes(array $filters)
    {
        return $this->taxRepo->all($filters);
    }

    public function createTax(array $data)
    {
        return $this->taxRepo->create($data);
    }

    public function getTaxById($id)
    {
        return $this->taxRepo->findById($id);
    }

    public function updateTax($id, array $data)
    {
        return $this->taxRepo->update($id, $data);
    }

    public function deleteTax($id)
    {
        return $this->taxRepo->delete($id);
    }
}
