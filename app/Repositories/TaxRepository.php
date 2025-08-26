<?php

namespace App\Repositories;

use App\Models\Tax;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class TaxRepository
{
    protected $model;

    public function __construct(Tax $model)
    {
        $this->model = $model;
    }

    public function all(array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->newQuery();

        if (isset($filters['search'])) {
            $query->where('name', 'like', "%{$filters['search']}%")
                  ->orWhere('rate', 'like', "%{$filters['search']}%");
        }

        $sortBy = $filters['sortBy'] ?? 'created_at';
        $sortDirection = $filters['sortDirection'] ?? 'desc';
        $perPage = $filters['perPage'] ?? 10;

        return $query->orderBy($sortBy, $sortDirection)->paginate($perPage);
    }

    public function create(array $data): Tax
    {
        return $this->model->create($data);
    }

    public function findById($id): ?Tax
    {
        return $this->model->find($id);
    }

    public function update($id, array $data): ?Tax
    {
        $tax = $this->findById($id);
        if ($tax) {
            $tax->update($data);
            return $tax;
        }
        return null;
    }

    public function delete($id): bool
    {
        $tax = $this->findById($id);
        if ($tax) {
            return $tax->delete();
        }
        return false;
    }
}
