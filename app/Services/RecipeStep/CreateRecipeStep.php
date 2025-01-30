<?php

namespace App\Services\RecipeStep;

use App\Models\RecipeStep;
use App\Models\Recipe;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CreateRecipeStep
{
    public function create(array $data): RecipeStep|string
    {
        try {
            DB::beginTransaction();

            // Ensure the recipe belongs to the authenticated user
            $recipe = Recipe::findOrFail($data['recipe_id']);
            if ($recipe->user_id !== Auth::id()) {
                throw new \Exception('You do not have permission to add steps to this recipe.');
            }

            // Check if a step with the same order already exists for the recipe
            $existingStep = RecipeStep::where('recipe_id', $data['recipe_id'])
                ->where('order', $data['order'])
                ->first();

            if ($existingStep) {
                // Find the next available order number
                $data['order'] = $this->getNextAvailableOrder($data['recipe_id'], $data['order']);
            }

            $recipeStep = RecipeStep::create($data);

            DB::commit();

            return $recipeStep;
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }

    protected function getNextAvailableOrder(int $recipeId, int $order): int
    {
        // Find the maximum order number for the recipe
        $maxOrder = RecipeStep::where('recipe_id', $recipeId)
            ->max('order');

        // If the requested order is greater than the max, use the requested order
        if ($order > $maxOrder) {
            return $order;
        }

        // Otherwise, find the next available order number
        $nextOrder = $order;
        while (RecipeStep::where('recipe_id', $recipeId)->where('order', $nextOrder)->exists()) {
            $nextOrder++;
        }

        return $nextOrder;
    }
}
