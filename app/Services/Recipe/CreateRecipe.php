<?php

namespace App\Services\Recipe;

use App\Models\Recipe;
use App\Services\Image\CreateImage;
use App\Services\Image\DeleteImage;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CreateRecipe
{
    public function __construct(
        protected CreateImage $createImageService,
        protected DeleteImage $deleteImageService
    ) {
    }

    public function create(array $data): Recipe
    {
        $imageData = null;

        /** @var UploadedFile|null $imageFile */
        if ($imageFile = data_get($data, 'image')) {
            $imageData = $this->createImageService->uploadOnly($imageFile);
        }

        DB::beginTransaction();

        try {
            $recipe = $this->createRecipe($data);
            $this->syncDiets($recipe, $data);
            $this->createIngredients($recipe, $data);
            $this->createSteps($recipe, $data);

            if ($imageData) {
                $this->createImageService->createDbRecord($recipe, $imageData);
            }

            DB::commit();

            return $recipe;

        } catch (Exception $e) {
            DB::rollback();

            if ($imageData) {
                $this->deleteImageService->deleteFile($imageData['path']);
            }

            throw $e;
        }
    }

    /**
     * Cria o registro principal da receita.
     */
    protected function createRecipe(array $data): Recipe
    {
        $recipeData = Arr::only($data, ['title', 'description', 'time', 'portion', 'difficulty', 'category_id']);
        $recipeData['user_id'] = Auth::id();
        return Recipe::create($recipeData);
    }

    /**
     * Sincroniza as dietas associadas à receita.
     */
    protected function syncDiets(Recipe $recipe, array $data): void
    {
        $diets = data_get($data, 'diets');
        throw_if(empty($diets), new Exception('As dietas são obrigatórias.'));
        $recipe->diets()->sync($diets);
    }

    /**
     * Cria os ingredientes da receita.
     */
    protected function createIngredients(Recipe $recipe, array $data): void
    {
        $ingredientsData = data_get($data, 'ingredients');
        throw_if(empty($ingredientsData), new Exception('Os ingredientes são obrigatórios.'));
        $recipe->ingredients()->createMany($ingredientsData);
    }

    /**
     * Cria os passos da receita.
     */
    protected function createSteps(Recipe $recipe, array $data): void
    {
        $stepsData = collect($data['steps'] ?? [])->map(function ($step, $index) {
            return [
                'order' => $index + 1,
                'description' => $step['description'],
            ];
        });

        if ($stepsData->isEmpty()) {
            throw new Exception('Os passos são obrigatórios.');
        }

        $recipe->steps()->createMany($stepsData->toArray());
    }
}
