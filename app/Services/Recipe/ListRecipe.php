<?php

namespace App\Services\Recipe;

use App\Models\Recipe;
use Illuminate\Support\Facades\Auth;

class ListRecipe
{
    public function list(array $filters = [], int $perPage = 10)
    {
        $query = Recipe::select('id', 'title', 'description', 'user_id', 'category_id')
            ->with([
                'diets' => fn($q) => $q->select('id', 'name'),
                'category' => fn($q) => $q->select('id', 'name'),
                'image' => fn($q) => $q->select('id', 'path', 'imageable_id', 'imageable_type'),
            ])
            ->withAvg('ratings', 'rating')
            ->withCount('ratings');

        if (Auth::check()) {
            $query->withExists([
                'favoritedByUsers as is_favorited' => fn($q) => $q->where('user_id', Auth::id()),
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
