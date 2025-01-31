<?php

namespace App\Services\RecipeStep;

use App\Models\RecipeStep;
use Illuminate\Support\Facades\DB;

class DeleteRecipeStep
{
    public function delete(RecipeStep $recipeStep): RecipeStep|string
    {
        try {
            DB::beginTransaction();

            if ($recipeStep->recipes()->exists()) {
                throw new \Exception('This step cannot be deleted because it is associated with one or more recipes');
            }

            $recipeStep->delete();

            DB::commit();

            return $recipeStep;
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
}
