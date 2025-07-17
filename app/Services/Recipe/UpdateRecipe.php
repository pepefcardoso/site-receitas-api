<?php

namespace App\Services\Recipe;

use App\Models\Recipe;
use App\Services\Image\CreateImage;
use App\Services\Image\UpdateImage;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class UpdateRecipe
{
    public function __construct(
        protected CreateImage $createImageService,
        protected UpdateImage $updateImageService
    ) {
    }

    public function update(int $id, array $data): Recipe
    {
        DB::beginTransaction();

        try {
            $recipe = Recipe::findOrFail($id);

            $this->updateRecipeDetails($recipe, $data);
            $this->syncDiets($recipe, $data);
            $this->handleIngredients($recipe, $data);
            $this->handleSteps($recipe, $data);

            if ($newImageFile = data_get($data, 'image')) {
                $recipe->image ? $this->updateImageService->update($recipe->image->id, $newImageFile)
                    : $this->createImageService->create($recipe, $newImageFile);
            }

            Cache::forget("recipe_model.{$id}");

            DB::commit();

            return $recipe;
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    protected function updateRecipeDetails(Recipe $recipe, array $data): void
    {
        $recipeData = Arr::only($data, ['title', 'description', 'time', 'portion', 'difficulty', 'category_id']);
        $recipe->update($recipeData);
    }

    protected function syncDiets(Recipe $recipe, array $data): void
    {
        $diets = Arr::get($data, 'diets');
        throw_if(empty($diets), new Exception('Diets are required'));
        $recipe->diets()->sync($diets);
    }

    protected function handleIngredients(Recipe $recipe, array $data): void
    {
        $incomingIngredients = collect($data['ingredients'] ?? []);
        $upsertData = $incomingIngredients->map(fn($item) => [
            'id' => $item['id'] ?? null,
            'recipe_id' => $recipe->id,
            'name' => $item['name'],
            'quantity' => $item['quantity'],
            'unit_id' => $item['unit_id'],
        ])->toArray();

        $recipe->ingredients()->upsert($upsertData, ['id'], ['name', 'quantity', 'unit_id']);
        $recipe->ingredients()->whereNotIn('id', $incomingIngredients->pluck('id')->filter())->delete();
    }

    protected function handleSteps(Recipe $recipe, array $data): void
    {
        $incomingSteps = collect($data['steps'] ?? []);
        $upsertData = $incomingSteps->map(fn($item, $index) => [
            'id' => $item['id'] ?? null,
            'recipe_id' => $recipe->id,
            'order' => $index + 1,
            'description' => $item['description'],
        ])->toArray();

        $recipe->steps()->upsert($upsertData, ['id'], ['order', 'description']);
        $recipe->steps()->whereNotIn('id', $incomingSteps->pluck('id')->filter())->delete();
    }
}
