<?php

namespace App\Services;

use App\Repositories\DepartmentRepository;
use App\Repositories\PositionRepository;

class PositionService
{
    /**
     * Create a new class instance.
     */
   protected $postitionRepository;

    public function __construct(PositionRepository $postitionRepository)
    {
        $this->postitionRepository = $postitionRepository;
    }

    public function getAllPosition()
    {
        return $this->postitionRepository->all();
    }

    public function getPositionById($id)
    {
        return $this->postitionRepository->find($id);
    }

    public function createPosition(array $data)
    {
        return $this->postitionRepository->create($data);
    }

    public function updatePosition($id, array $data)
    {
        return $this->postitionRepository->update($id, $data);
    }

    public function deletePosition($id)
    {
        return $this->postitionRepository->delete($id);
    }

}
