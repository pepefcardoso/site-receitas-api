<?php

namespace App\Services\Recipe;

use App\Models\Recipe;
use App\Services\Image\CreateImage;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CreateRecipe
{
    public function __construct(
        protected CreateImage $createImageService
    )
    {
    }

    public function create(array $data): Recipe
    {
        DB::beginTransaction();

        try {
            $recipe = $this->createRecipe($data);
            $this->syncDiets($recipe, $data);
            $this->createIngredients($recipe, $data);
            $this->createSteps($recipe, $data);

            if ($image = data_get($data, 'image')) {
                $this->createImageService->create($recipe, $image);
            }

            DB::commit();

            return $recipe;
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    protected function createRecipe(array $data): Recipe
    {
        $recipeData = Arr::only($data, ['title', 'description', 'time', 'portion', 'difficulty', 'category_id']);
        $recipeData['user_id'] = Auth::id();
        return Recipe::create($recipeData);
    }

    protected function syncDiets(Recipe $recipe, array $data): void
    {
        $diets = data_get($data, 'diets');
        throw_if(empty($diets), new Exception('Diets are required'));
        $recipe->diets()->sync($diets);
    }

    protected function createIngredients(Recipe $recipe, array $data): void
    {
        $ingredientsData = data_get($data, 'ingredients');
        throw_if(empty($ingredientsData), new Exception('Ingredients are required'));
        $recipe->ingredients()->createMany($ingredientsData);
    }

    protected function createSteps(Recipe $recipe, array $data): void
    {
        $stepsData = collect($data['steps'] ?? [])->map(function ($step, $index) {
            return [
                'order' => $index + 1,
                'description' => $step['description'],
            ];
        });
        $recipe->steps()->createMany($stepsData->toArray());
    }
}
