<?php

namespace App\Services\RecipeUnit;

use App\Models\RecipeUnit;
use Illuminate\Support\Facades\DB;

class UpdateRecipeUnit
{
    public function update(int $recipeUnitId, array $data)
    {
        try {
            DB::beginTransaction();

            $recipeUnit = RecipeUnit::findOrFail($recipeUnitId);
            $recipeUnit->fill($data);
            $recipeUnit->save();

            DB::commit();

            return $recipeUnit;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
