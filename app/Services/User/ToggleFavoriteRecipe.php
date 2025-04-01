<?php

namespace App\Services\User;

use App\Models\Recipe;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

class ToggleFavoriteRecipe
{
    public function toggle(array $data): User|string
    {
        try {
            DB::beginTransaction();

            $user = User::findOrFail(auth()->user()->id);

            $recipeId = data_get($data, 'recipe_id');
            $recipe = Recipe::findOrFail($recipeId);

            if ($user->favoriteRecipes()->where('recipe_id', $recipeId)->exists()) {
                $user->favoriteRecipes()->detach($recipe->id);
                $message = "Receita removida das favoritas";
            } else {
                $user->favoriteRecipes()->attach($recipe->id);
                $message = "Receita favoritada com sucesso";
            }

            DB::commit();

            return $message;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
