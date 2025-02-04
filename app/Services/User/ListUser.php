<?php

namespace App\Services\User;

use App\Models\User;

class ListUser
{
    public function list(array $filters = [], $perPage = 10)
    {
        $query = User::with([
            'image',
        ]);

        $query = $query->filter($filters);

        if (isset($filters['order_by']) && isset($filters['order_direction'])) {
            $query->orderBy($filters['order_by'], $filters['order_direction']);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query->paginate($perPage);
    }
}
