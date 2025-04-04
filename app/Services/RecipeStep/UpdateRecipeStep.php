<?php

namespace App\Services\RecipeStep;

use App\Models\RecipeStep;
use Illuminate\Support\Facades\DB;

class UpdateRecipeStep
{
    public function update(int $recipeStepId, array $data): RecipeStep|string
    {
        try {
            DB::beginTransaction();

            $recipeStep = RecipeStep::findOrFail($recipeStepId);
            unset($data['order']);
            $recipeStep->update($data);

            DB::commit();

            return $recipeStep;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
