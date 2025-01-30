<?php

namespace App\Services\RecipeIngredient;

use App\Models\RecipeIngredient;
use App\Models\Recipe;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UpdateRecipeIngredient
{
    public function update(RecipeIngredient $recipeIngredient, array $data)
    {
        try {
            DB::beginTransaction();

            // Ensure the recipe belongs to the authenticated user
            $recipe = Recipe::findOrFail($recipeIngredient->recipe_id);
            if ($recipe->user_id !== Auth::id()) {
                throw new \Exception('You do not have permission to update ingredients for this recipe.');
            }

            $recipeIngredient->fill($data);
            $recipeIngredient->save();

            DB::commit();

            return $recipeIngredient;
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
}
