<?php

namespace App\Services\RecipeStep;

use App\Models\RecipeStep;
use Illuminate\Support\Facades\DB;

class CreateRecipeStep
{
    public function create(array $data)
    {
        try {
            DB::beginTransaction();

            $recipeStep = RecipeStep::create($data);

            DB::commit();

            return $recipeStep;
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
}
