<?php

namespace App\Services\Post;

use App\Models\Post;

class ListPost
{
    public function list(array $filters = [], $perPage = 10)
    {
        $query = Post::with([
            'category',
            'topics',
            'image'
        ]);

        $query = $query->filter($filters);

        $query = $this->applySorting($query, $filters);

        return $query->paginate($perPage);
    }

    protected function applySorting($query, array $filters)
    {
        $orderBy = $filters['order_by'] ?? 'created_at';
        $orderDirection = $filters['order_direction'] ?? 'desc';

        if (!in_array($orderBy, Post::VALID_SORT_COLUMNS)) {
            $orderBy = 'created_at';
        }

        if (!in_array(strtolower($orderDirection), ['asc', 'desc'])) {
            $orderDirection = 'desc';
        }

        return $query->orderBy($orderBy, $orderDirection);
    }
}
