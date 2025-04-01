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

            $recipeIngredient->fill($data);
            $recipeIngredient->save();

            DB::commit();

            return $recipeIngredient;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
