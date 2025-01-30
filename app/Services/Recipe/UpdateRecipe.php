<?php

namespace App\Services\Recipe;

use App\Models\Recipe;
use App\Services\RecipeIngredient\CreateRecipeIngredient;
use App\Services\RecipeIngredient\DeleteRecipeIngredient;
use App\Services\RecipeIngredient\UpdateRecipeIngredient;
use App\Services\RecipeStep\CreateRecipeStep;
use App\Services\RecipeStep\DeleteRecipeStep;
use App\Services\RecipeStep\UpdateRecipeStep;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class UpdateRecipe
{
    public function __construct(
        protected CreateRecipeIngredient $createRecipeIngredient,
        protected UpdateRecipeIngredient $updateRecipeIngredient,
        protected DeleteRecipeIngredient $deleteRecipeIngredient,
        protected CreateRecipeStep $createRecipeStep,
        protected UpdateRecipeStep $updateRecipeStep,
        protected DeleteRecipeStep $deleteRecipeStep
    ) {
    }

    public function update(int $id, array $data): Recipe | string
    {
        try {
            DB::beginTransaction();

            $recipe = Recipe::findOrFail($id);

            if ($recipe->user_id !== auth()->id()) {
                throw new Exception("Unauthorized: You don't own this recipe.");
            }

            $this->updateRecipeDetails($recipe, $data);
            $this->syncDiets($recipe, $data);
            $this->handleIngredients($recipe, $data);
            $this->handleSteps($recipe, $data);

            DB::commit();
            return $recipe;
        } catch (Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }


    protected function updateRecipeDetails(Recipe $recipe, array $data): void
    {
        $recipeData = Arr::only($data, ['title', 'description', 'time', 'portion', 'difficulty', 'image', 'category_id']);
        $recipe->update($recipeData);
    }

    protected function syncDiets(Recipe $recipe, array $data): void
    {
        $diets = Arr::get($data, 'diets');
        throw_if(empty($diets), Exception::class, 'Diets are required');
        $recipe->diets()->sync($diets);
    }

    protected function handleIngredients(Recipe $recipe, array $data): void
    {
        if (isset($data['ingredients'])) {
            $ingredientIds = collect($data['ingredients'])->map(function ($ingredient) use ($recipe) {
                if (isset($ingredient['id'])) {
                    $this->updateRecipeIngredient->update($ingredient['id'], [
                        'quantity' => $ingredient['quantity'],
                        'name' => $ingredient['name'],
                        'unit_id' => $ingredient['unit_id'],
                    ]);
                    return $ingredient['id'];
                } else {
                    $newIngredient = $this->createRecipeIngredient->create([
                        'recipe_id' => $recipe->id,
                        'quantity' => $ingredient['quantity'],
                        'name' => $ingredient['name'],
                        'unit_id' => $ingredient['unit_id'],
                    ]);
                    return $newIngredient->id;
                }
            })->filter()->values()->toArray();

            $recipe->ingredients()->whereNotIn('id', $ingredientIds)->delete();
        }
    }

    protected function handleSteps(Recipe $recipe, array $data): void
    {
        if (isset($data['steps'])) {
            $stepIds = collect($data['steps'])->map(function ($step) use ($recipe) {
                if (isset($step['id'])) {
                    $this->updateRecipeStep->update($step['id'], [
                        'order' => $step['order'],
                        'description' => $step['description'],
                    ]);
                    return $step['id'];
                } else {
                    $newStep = $this->createRecipeStep->create([
                        'recipe_id' => $recipe->id,
                        'order' => $step['order'],
                        'description' => $step['description'],
                    ]);
                    return $newStep->id;
                }
            })->filter()->values()->toArray();

            $recipe->steps()->whereNotIn('id', $stepIds)->delete();
        }
    }
}
