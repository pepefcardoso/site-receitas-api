<?php

namespace App\Services\Recipe;

use App\Models\Recipe;
use App\Services\Image\CreateImage;
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
    protected CreateImage $createImageService;

    public function __construct(
        CreateRecipeIngredient $createRecipeIngredient,
        CreateRecipeStep       $createRecipeStep,
        CreateImage            $createImageService,
    )
    {
        $this->createRecipeIngredient = $createRecipeIngredient;
        $this->createRecipeStep = $createRecipeStep;
        $this->createImageService = $createImageService;
    }

    public function create(array $data): Recipe
    {
        DB::beginTransaction();

        try {
            $recipe = $this->createRecipe($data);
            $this->syncDiets($recipe, $data);
            $this->createIngredients($recipe, $data);
            $this->createSteps($recipe, $data);

            $image = data_get($data, 'image');
            $this->createImageService->create($recipe, $image);

            DB::commit();

            return $recipe;
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
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
        $steps = $data['steps'] ?? [];

        foreach ($steps as $index => $step) {
            $recipe->steps()->create([
                'order' => $index + 1,
                'description' => $step['description'],
            ]);
        }
    }
}
