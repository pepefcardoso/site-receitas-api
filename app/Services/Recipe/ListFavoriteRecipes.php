<?php

namespace App\Services\Recipe;

use App\Models\User;
use Exception;

class ListFavoriteRecipes
{
    public function list(int $perPage = 10)
    {
        $userId = auth()->id();
        if (!$userId) {
            throw new Exception('User not authenticated');
        }

        return User::find($userId)
            ->favoriteRecipes()
            ->with(['image'])
            ->paginate($perPage);
    }
}
