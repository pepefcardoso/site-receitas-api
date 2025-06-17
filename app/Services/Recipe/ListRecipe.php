<?php

namespace App\Services\Recipe;

use App\Models\Recipe;

class ListRecipe
{
    public function list(array $filters = [], int $perPage = 10)
    {
        $query = Recipe::with([
            'diets',
            'category',
            'image',
        ])
            ->withAvg('ratings', 'rating')
            ->withCount('ratings');

        if ($userId = auth('sanctum')->id()) {
            $query->withExists([
                'favoritedByUsers as is_favorited' => fn($q) => $q->where('user_id', $userId),
            ]);
        }

        $query->filter($filters);
        $this->applySorting($query, $filters);

        return $query->paginate($perPage);
    }


    protected function applySorting($query, array $filters)
    {
        $orderBy = $filters['order_by'] ?? 'created_at';
        $orderDirection = $filters['order_direction'] ?? 'desc';

        if (!in_array($orderBy, Recipe::VALID_SORT_COLUMNS)) {
            $orderBy = 'created_at';
        }

        if (!in_array(strtolower($orderDirection), ['asc', 'desc'])) {
            $orderDirection = 'desc';
        }

        return $query->orderBy($orderBy, $orderDirection);
    }
}
