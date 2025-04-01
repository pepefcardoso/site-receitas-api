<?php

namespace App\Services\RecipeUnit;

use App\Models\RecipeUnit;
use Illuminate\Support\Facades\DB;

class DeleteRecipeUnit
{
    public function delete(RecipeUnit $recipeUnit)
    {
        try {
            DB::beginTransaction();

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
