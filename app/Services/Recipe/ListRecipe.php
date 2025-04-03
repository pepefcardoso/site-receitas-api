<?php

namespace App\Services\Recipe;

use App\Models\Recipe;
use Illuminate\Support\Facades\Auth;

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

        if (Auth::check()) {
            $query->withExists([
                'favoritedByUsers as is_favorited' => function ($query) {
                    $query->where('user_id', Auth::id());
                }
            ]);
        }

        $query = $query->filter($filters);
        $query = $this->applySorting($query, $filters);

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
