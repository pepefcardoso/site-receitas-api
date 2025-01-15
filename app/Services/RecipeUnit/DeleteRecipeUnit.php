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

            $recipeUnit->delete();

            DB::commit();

            return $recipeUnit;
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
}
