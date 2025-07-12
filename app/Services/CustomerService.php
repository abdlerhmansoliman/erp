<?php

namespace App\Services;

use App\Repositories\Interfaces\CustomerRepositoryInterface;


class CustomerService
{
    /**
     * Create a new class instance.
     */
    protected $customerRepository;
    public function __construct(CustomerRepositoryInterface $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }
    public function getAllCustomers()
    {
        return $this->customerRepository->getAllCustomers();
    }
    public function findById(int $id)
    {
        return $this->customerRepository->findById($id);
    }
    public function create(array $data)
    {
        return $this->customerRepository->create($data);
    }
    public function delete(int $id): bool
    {
        return $this->customerRepository->delete($id);
    }
    public function update(int $id, array $data)
    {
        return $this->customerRepository->update($id, $data);
    }
    

}
