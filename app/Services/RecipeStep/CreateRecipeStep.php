<?php

namespace App\Services\RecipeStep;

use App\Models\RecipeStep;
use Illuminate\Support\Facades\DB;

class CreateRecipeStep
{
    public function create(array $data): RecipeStep|string
    {
        try {
            DB::beginTransaction();

            $data['order'] = (RecipeStep::where('recipe_id', $data['recipe_id'])->max('order') ?? 0) + 1;
            $recipeStep = RecipeStep::create($data);

            DB::commit();

            return $recipeStep;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
