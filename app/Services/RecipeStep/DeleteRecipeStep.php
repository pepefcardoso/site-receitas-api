<?php

namespace App\Services\RecipeStep;

use App\Models\RecipeStep;
use Illuminate\Support\Facades\DB;

class DeleteRecipeStep
{
    public function delete(int $recipeStepId): RecipeStep|string
    {
        try {
            DB::beginTransaction();

            $recipeStep = RecipeStep::findOrFail($recipeStepId);
            $recipeStep->delete();

            DB::commit();

            return $recipeStep;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
