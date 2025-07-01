<?php

namespace App\Services\Recipe;

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

        return Recipe::where('user_id', $userId)
            ->with([
                'image' => fn($q) => $q
                    ->select('id', 'model_type', 'model_id', 'name', 'file_name'),
            ])
            ->withExists([
                'favoritedByUsers as is_favorited' => fn($q) => $q->where('user_id', $userId)
            ])
            ->withAvg('ratings', 'rating')
            ->withCount('ratings')
            ->paginate($perPage);
    }
}
