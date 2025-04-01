<?php

namespace App\Services\RecipeIngredient;

use App\Models\RecipeIngredient;
use Illuminate\Support\Facades\DB;

class DeleteRecipeIngredient
{
    public function delete(RecipeIngredient $recipeIngredient)
    {
        try {
            DB::beginTransaction();

            $recipeIngredient->delete();

            DB::commit();

            return $recipeIngredient;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
