<?php

namespace App\Services\Recipe;

use App\Models\Recipe;
use App\Models\RecipeIngredient;
use App\Services\Image\UpdateImage;
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
        protected DeleteRecipeStep $deleteRecipeStep,
        protected UpdateImage $updateImageService
    ) {
    }

    public function update(int $id, array $data): Recipe|string
    {
        DB::beginTransaction();

        try {
            $recipe = Recipe::findOrFail($id);

            $this->updateRecipeDetails($recipe, $data);
            $this->syncDiets($recipe, $data);
            $this->handleIngredients($recipe, $data);
            $this->handleSteps($recipe, $data);

            $newImageFile = data_get($data, 'image');
            if ($newImageFile) {
                $currentImage = $recipe->image;
                $this->updateImageService->update($currentImage->id, $newImageFile);
            }

            DB::commit();

            return $recipe;
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
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
        if (!isset($data['ingredients'])) {
            $recipe->ingredients->each(function ($ingredient) {
                $this->deleteRecipeIngredient->delete($ingredient->id);
            });
            return;
        }

        $ingredientIds = [];

        foreach ($data['ingredients'] as $ingredient) {
            if (isset($ingredient['id'])) {
                $currentIngredient = RecipeIngredient::findOrFail($ingredient['id']);
                $this->updateRecipeIngredient->update($currentIngredient->id, [
                    'quantity' => $ingredient['quantity'],
                    'name' => $ingredient['name'],
                    'unit_id' => $ingredient['unit_id'],
                ]);
                $ingredientIds[] = $ingredient['id'];
            } else {
                $newIngredient = $this->createRecipeIngredient->create([
                    'recipe_id' => $recipe->id,
                    'quantity' => $ingredient['quantity'],
                    'name' => $ingredient['name'],
                    'unit_id' => $ingredient['unit_id'],
                ]);
                $ingredientIds[] = $newIngredient->id;
            }
        }

        $recipe->ingredients()
            ->whereNotIn('id', $ingredientIds)
            ->get()
            ->each(function ($ingredient) {
                $this->deleteRecipeIngredient->delete($ingredient->id);
            });
    }

    protected function handleSteps(Recipe $recipe, array $data): void
    {
        $steps = $data['steps'] ?? [];

        $stepIds = [];

        foreach ($steps as $index => $step) {
            $stepData = [
                'order' => $index + 1,
                'description' => $step['description'],
                'recipe_id' => $recipe->id,
            ];

            if (isset($step['id'])) {
                $currentStep = $recipe->steps()->findOrFail($step['id']);
                $currentStep->update($stepData);
                $stepIds[] = $currentStep->id;
            } else {
                $newStep = $recipe->steps()->create($stepData);
                $stepIds[] = $newStep->id;
            }
        }

        $recipe->steps()->whereNotIn('id', $stepIds)->delete();
    }
}
