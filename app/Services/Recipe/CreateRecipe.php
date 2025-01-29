<?php

namespace App\Services\Recipe;

use App\Models\Recipe;
use App\Services\RecipeIngredient\CreateRecipeIngredient;
use App\Services\RecipeStep\CreateRecipeStep;
use Illuminate\Support\Facades\DB;

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

    public function create(array $data)
    {
        try {
            DB::beginTransaction();

            $recipeData = [
                'title' => $data['title'],
                'description' => $data['description'],
                'time' => $data['time'],
                'portion' => $data['portion'],
                'difficulty' => $data['difficulty'],
                'image' => $data['image'],
                'category_id' => $data['category_id'],
            ];

            $recipe = Recipe::create($recipeData);

            if (isset($data['diets'])) {
                $recipe->diets()->sync($data['diets']);
            }

            if (isset($data['ingredients'])) {
                foreach ($data['ingredients'] as $ingredient) {
                    $this->createRecipeIngredient->create([
                        'recipe_id' => $recipe->id,
                        'quantity' => $ingredient['quantity'],
                        'name' => $ingredient['name'],
                        'unit_id' => $ingredient['unit_id'],
                    ]);
                }
            }

            if (isset($data['steps'])) {
                foreach ($data['steps'] as $step) {
                    $this->createRecipeStep->create([
                        'recipe_id' => $recipe->id,
                        'order' => $step['order'],
                        'description' => $step['description'],
                    ]);
                }
            }

            DB::commit();

            return $recipe;
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
}
