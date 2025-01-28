<?php

namespace App\Services\RecipeIngredient;

use App\Models\RecipeIngredient;
use App\Models\RecipeUnit;
use Illuminate\Support\Facades\DB;

class CreateRecipeIngredient
{
    public function create(array $data)
    {
        try {
            DB::beginTransaction();

            throw_if(!isset($data['unit_id']), \Exception::class, 'Unit Id is required');
            $recipeUnit = RecipeUnit::findOrFail($data['unit_id']);
            throw_if(!$recipeUnit, \Exception::class, 'Unit not found');

            $recipeIngredient = RecipeIngredient::create($data);

            DB::commit();

            return $recipeIngredient;
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
}
