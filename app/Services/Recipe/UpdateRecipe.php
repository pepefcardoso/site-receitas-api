<?php

namespace App\Services\Recipe;

use App\Models\Recipe;
use App\Services\RecipeIngredient\CreateRecipeIngredient;
use App\Services\RecipeStep\CreateRecipeStep;
use Illuminate\Support\Facades\DB;

class UpdateRecipe
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

    public function update(int $id, array $data)
    {
        try {
            DB::beginTransaction();

            // Find the recipe by ID
            $recipe = Recipe::findOrFail($id);

            // Prepare the data to be updated
            $recipeData = [
                'title' => $data['title'],
                'description' => $data['description'],
                'time' => $data['time'],
                'portion' => $data['portion'],
                'difficulty' => $data['difficulty'],
                'image' => $data['image'],
                'category_id' => $data['category_id'], // Update category_id
            ];

            // Update the recipe
            $recipe->update($recipeData);

            // Sync the Recipe Diets (Many-to-Many Relationship)
            if (isset($data['diets'])) {
                $recipe->diets()->sync($data['diets']); // Sync existing diets
            }

            // Update the Recipe Ingredients (One-to-Many Relationship)
            if (isset($data['ingredients'])) {
                foreach ($data['ingredients'] as $ingredient) {
                    // Check if ingredient exists, otherwise create
                    $existingIngredient = $recipe->ingredients()->where('name', $ingredient['name'])->first();
                    if ($existingIngredient) {
                        $existingIngredient->update([
                            'quantity' => $ingredient['quantity'],
                            'unit_id' => $ingredient['unit_id'],
                        ]);
                    } else {
                        $this->createRecipeIngredient->create([
                            'recipe_id' => $recipe->id,
                            'quantity' => $ingredient['quantity'],
                            'name' => $ingredient['name'],
                            'unit_id' => $ingredient['unit_id'],
                        ]);
                    }
                }
            }

            // Update the Recipe Steps (One-to-Many Relationship)
            if (isset($data['steps'])) {
                foreach ($data['steps'] as $step) {
                    // Check if step exists, otherwise create
                    $existingStep = $recipe->steps()->where('order', $step['order'])->first();
                    if ($existingStep) {
                        $existingStep->update([
                            'description' => $step['description'],
                        ]);
                    } else {
                        $this->createRecipeStep->create([
                            'recipe_id' => $recipe->id,
                            'order' => $step['order'],
                            'description' => $step['description'],
                        ]);
                    }
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
