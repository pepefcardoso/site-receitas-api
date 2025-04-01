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
        ]);

        if (Auth::check()) {
            $userId = Auth::id();
            $query->addSelect([
                'is_favorited' => function ($query) use ($userId) {
                    $query->selectRaw('COUNT(*)')
                        ->from('rl_user_favorite_recipes')
                        ->whereColumn('recipe_id', 'recipes.id')
                        ->where('user_id', $userId);
                }
            ])->withCasts(['is_favorited' => 'boolean']);
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
