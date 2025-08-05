<?php

namespace App\Services\Company;

use App\Models\Company;

class ListCompanies
{
    public function list(array $filters = [], $perPage = 10)
    {
        $query = Company::select('id', 'name', 'cnpj', 'email', 'phone', 'address', 'website', 'user_id', 'created_at')
            ->with([
                'image' => fn($q) => $q->select('id', 'path as url', 'imageable_id', 'imageable_type')
            ])
            ->filter($filters);

        if (isset($filters['order_by'], $filters['order_direction'])) {
            $query->orderBy($filters['order_by'], $filters['order_direction']);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query->paginate(
            $filters['per_page'] ?? $perPage
        );
    }
}
