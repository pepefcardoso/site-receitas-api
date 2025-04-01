<?php

namespace App\Services\Recipe;

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class ListFavorites
{
    public function list(array $filters = [], int $perPage = 10)
    {
        $userId = data_get($filters, 'user_id');
        $user = User::findOrFail($userId);

        $query = $user->favoriteRecipes()
            ->with(['category', 'diets', 'image', 'user', 'steps', 'ingredients.unit'])
            ->withExists([
                'favoritedByUsers as is_favorited' => function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                }
            ]);

        if (!empty($filters['title'])) {
            $query->where('title', 'like', '%' . $filters['title'] . '%');
        }

        $orderBy = in_array($filters['order_by'] ?? 'created_at', Recipe::VALID_SORT_COLUMNS)
            ? $filters['order_by']
            : 'created_at';

        $orderDirection = in_array(strtolower($filters['order_direction'] ?? 'desc'), ['asc', 'desc'])
            ? $filters['order_direction']
            : 'desc';

        return $query->orderBy($orderBy, $orderDirection)
            ->paginate($perPage);
    }
}
