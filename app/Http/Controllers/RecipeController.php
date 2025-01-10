<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\RecipeIngredient;
use App\Models\RecipeStep;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class RecipeController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum', except: ['index', 'show'])
        ];
    }

    public function index()
    {
        $recipes = Recipe::with(['user', 'category', 'diets'])->get();
        return response()->json($recipes, 201);
    }

    public function store(Request $request)
    {
        $fields = $request->validate(Recipe::rules());

        $recipe = $request->user()->recipes()->create($fields);

        foreach ($fields['steps'] as $stepData) {
            $stepValidator = Validator::make($stepData, RecipeStep::rules());

            if ($stepValidator->fails()) {
                return response()->json(['errors' => $stepValidator->errors()], 400);
            }

            $recipe->steps()->create([
                'order' => $stepData['order'],
                'description' => $stepData['description'],
                'recipe_id' => $recipe->id
            ]);
        }

        foreach ($fields['ingredients'] as $ingredientsData) {
            $ingredientsValidator = Validator::make($ingredientsData, RecipeIngredient::rules());

            if ($ingredientsValidator->fails()) {
                return response()->json(['errors' => $ingredientsValidator->errors()], 400);
            }

            $recipe->ingredients()->create([
                'quantity' => $ingredientsData['quantity'],
                'name' => $ingredientsData['name'],
                'recipe_id' => $recipe->id
            ]);
        }

        $recipe->diets()->sync($request->diets);

        return response()->json($recipe->load(['user', 'category', 'diets', 'steps', 'ingredients']), 201);
    }

    public function show(Recipe $recipe)
    {
        return response()->json($recipe->load(['user', 'category', 'diets', 'steps', 'ingredients']), 200);
    }

    public function update(Recipe $recipe)
    {
        Gate::authorize('modify', $recipe);

        $fields = request()->validate(Recipe::rules());

        $recipe->update($fields);

        $recipe->steps()->delete();
        foreach ($fields['steps'] as $stepData) {
            $stepValidator = Validator::make($stepData, RecipeStep::rules());

            if ($stepValidator->fails()) {
                return response()->json(['errors' => $stepValidator->errors()], 400);
            }

            $recipe->steps()->create([
                'order' => $stepData['order'],
                'description' => $stepData['description'],
                'recipe_id' => $recipe->id
            ]);
        }

        foreach ($fields['ingredients'] as $ingredientsData) {
            $ingredientsValidator = Validator::make($ingredientsData, RecipeIngredient::rules());

            if ($ingredientsValidator->fails()) {
                return response()->json(['errors' => $ingredientsValidator->errors()], 400);
            }

            $recipe->ingredients()->create([
                'quantity' => $ingredientsData['quantity'],
                'name' => $ingredientsData['name'],
                'recipe_id' => $recipe->id
            ]);
        }

        if (request()->has('diets')) {
            $recipe->diets()->sync(request()->diets);
        }

        return response()->json($recipe->load(['user', 'category', 'diets', 'steps', 'ingredients']), 201);
    }

    public function destroy(Recipe $recipe)
    {
        Gate::authorize('modify', $recipe);

        $recipe->delete();

        return response()->json(null, 204);
    }
}
