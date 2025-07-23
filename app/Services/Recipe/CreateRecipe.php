<?php

namespace App\Services\Recipe;

use App\Models\Recipe;
use App\Services\Image\CreateImage;
use App\Services\Image\DeleteImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class CreateRecipe
{
    public function __construct(
        protected CreateImage $createImageService,
        protected DeleteImage $deleteImageService
    ) {
    }

    /**
     * Cria uma nova receita completa de forma segura.
     *
     * @param array $data
     * @return Recipe
     * @throws Throwable
     */
    public function create(array $data): Recipe
    {
        $imageData = null;

        try {
            /** @var UploadedFile|null $imageFile */
            if ($imageFile = data_get($data, 'image')) {
                $imageData = $this->createImageService->uploadOnly($imageFile);
            }

            $recipe = DB::transaction(function () use ($data, $imageData) {
                $recipeData = Arr::only($data, ['title', 'description', 'time', 'portion', 'difficulty', 'category_id']);
                $recipeData['user_id'] = Auth::id();
                $recipe = Recipe::create($recipeData);

                $this->syncDiets($recipe, $data);
                $this->createIngredients($recipe, $data);
                $this->createSteps($recipe, $data);

                if ($imageData) {
                    $this->createImageService->createDbRecord($recipe, $imageData);
                }

                return $recipe;
            });

            return $recipe;

        } catch (Throwable $e) {
            if ($imageData) {
                Log::info('Rolling back file upload due to DB transaction failure for recipe.', [
                    'path' => $imageData['path'],
                    'error' => $e->getMessage(),
                ]);
                $this->deleteImageService->deleteFile($imageData['path']);
            }

            throw $e;
        }
    }

    /**
     * Sincroniza as dietas associadas à receita.
     */
    protected function syncDiets(Recipe $recipe, array $data): void
    {
        if ($diets = data_get($data, 'diets')) {
            $recipe->diets()->sync($diets);
        }
    }

    /**
     * Cria os ingredientes da receita.
     */
    protected function createIngredients(Recipe $recipe, array $data): void
    {
        if ($ingredientsData = data_get($data, 'ingredients')) {
            $recipe->ingredients()->createMany($ingredientsData);
        }
    }

    /**
     * Cria os passos da receita.
     */
    protected function createSteps(Recipe $recipe, array $data): void
    {
        if ($stepsData = data_get($data, 'steps')) {
            $steps = collect($stepsData)->map(fn($step, $index) => [
                'order' => $index + 1,
                'description' => $step['description'],
            ]);

            $recipe->steps()->createMany($steps->toArray());
        }
    }
}
