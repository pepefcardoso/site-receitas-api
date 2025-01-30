<?php

namespace App\Services\Recipe;

use App\Models\Recipe;
use App\Services\RecipeIngredient\CreateRecipeIngredient;
use App\Services\RecipeStep\CreateRecipeStep;
use Illuminate\Support\Arr;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CreateRecipe
{
    protected CreateRecipeIngredient $createRecipeIngredient;
    protected CreateRecipeStep $createRecipeStep;

    public function __construct(
        CreateRecipeIngredient $createRecipeIngredient,
        CreateRecipeStep       $createRecipeStep
    )
    {
        $this->createRecipeIngredient = $createRecipeIngredient;
        $this->createRecipeStep = $createRecipeStep;
    }

    public function create(array $data): Recipe
    {
        DB::beginTransaction();

        try {
            $recipe = $this->createRecipe($data);
            $this->syncDiets($recipe, $data);
            $this->createIngredients($recipe, $data);
            $this->createSteps($recipe, $data);

            DB::commit();

            return $recipe;
        } catch (Exception $e) {
            DB::rollback();
            throw new Exception("Failed to create recipe: " . $e->getMessage());
        }
    }

    protected function createRecipe(array $data): Recipe
    {
        $userId = Auth::id();

        $recipeData = array_merge(
            Arr::only($data, ['title', 'description', 'time', 'portion', 'difficulty', 'image', 'category_id']),
            ['user_id' => $userId]
        );

        return Recipe::create($recipeData);
    }

    protected function syncDiets(Recipe $recipe, array $data): void
    {
        $diets = data_get($data, 'diets');
        throw_if(empty($diets), Exception::class, 'Diets are required');
        $recipe->diets()->sync($diets);
    }

    protected function createIngredients(Recipe $recipe, array $data): void
    {
        $ingredients = data_get($data, 'ingredients');
        throw_if(empty($ingredients), Exception::class, 'Ingredients are required');

        foreach ($ingredients as $ingredient) {
            $this->createRecipeIngredient->create([
                'recipe_id' => $recipe->id,
                'quantity' => $ingredient['quantity'],
                'name' => $ingredient['name'],
                'unit_id' => $ingredient['unit_id'],
            ]);
        }
    }

    protected function createSteps(Recipe $recipe, array $data): void
    {
        $steps = data_get($data, 'steps');
        throw_if(empty($steps), Exception::class, 'Steps are required');

        foreach ($steps as $step) {
            $this->createRecipeStep->create([
                'recipe_id' => $recipe->id,
                'order' => $step['order'],
                'description' => $step['description'],
            ]);
        }
    }
}
