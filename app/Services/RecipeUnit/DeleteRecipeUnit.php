<?php

namespace App\Services\RecipeUnit;

use App\Models\RecipeUnit;
use Illuminate\Support\Facades\DB;

class DeleteRecipeUnit
{
    public function delete(int $recipeUnitId)
    {
        try {
            DB::beginTransaction();

            $recipeUnit = RecipeUnit::findOrFail($recipeUnitId);
            if ($recipeUnit->ingredients()->exists()) {
                throw new \Exception('This unit cannot be deleted because it is associated with one or more ingredients');
            }

            $recipeUnit->delete();

            DB::commit();

            return $recipeUnit;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
