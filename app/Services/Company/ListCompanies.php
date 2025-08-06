<?php

namespace App\Services\Company;

use App\Models\Company;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListCompanies
{
    public function list(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = Company::with(['image'])
            ->filter($filters);

        $orderBy = $filters['order_by'] ?? 'created_at';
        $orderDirection = $filters['order_direction'] ?? 'desc';
        $query->orderBy($orderBy, $orderDirection);

        return $query->paginate($filters['per_page'] ?? $perPage);
    }
}
