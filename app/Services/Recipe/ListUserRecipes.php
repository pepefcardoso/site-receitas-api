<?php

namespace App\Services\Post;

use App\Models\Recipe;
use Exception;
use Illuminate\Support\Facades\Auth;

class ListUserRecipes
{
    public function list($perPage = 10)
    {
        $userId = Auth::id();
        if (!$userId) {
            throw new Exception('User not authenticated');
        }

        return Recipe::with(['image'])
            ->withAvg('ratings', 'rating')
            ->withCount('ratings')
            ->where('user_id', $userId)
            ->paginate($perPage);
    }
}
