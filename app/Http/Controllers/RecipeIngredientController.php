<?php

namespace App\Http\Controllers;

use App\Models\RecipeIngredient;
use App\Services\RecipeIngredient\CreateRecipeIngredient;
use App\Services\RecipeIngredient\DeleteRecipeIngredient;
use App\Services\RecipeIngredient\ListRecipeIngredient;
use App\Services\RecipeIngredient\ShowRecipeIngredient;
use App\Services\RecipeIngredient\UpdateRecipeIngredient;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class RecipeIngredientController extends BaseController
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    public function index(Request $request, ListRecipeIngredient $service)
    {
        return $this->execute(function () use ($service) {
            $ingredients = $service->list();
            return response()->json($ingredients);
        });
    }

    public function store(Request $request, CreateRecipeIngredient $service)
    {
        return $this->execute(function () use ($request, $service) {
            $this->authorize('create', RecipeIngredient::class);

            $data = $request->validate(RecipeIngredient::createRules());
            $ingredient = $service->create($data);

            return response()->json($ingredient, 201);
        });
    }

    public function show(RecipeIngredient $recipeIngredient, ShowRecipeIngredient $service)
    {
        return $this->execute(function () use ($recipeIngredient, $service) {
            $ingredient = $service->show($recipeIngredient->id);
            return response()->json($ingredient);
        });
    }

    public function update(Request $request, RecipeIngredient $recipeIngredient, UpdateRecipeIngredient $service)
    {
        return $this->execute(function () use ($request, $recipeIngredient, $service) {
            $this->authorize('update', $recipeIngredient);

            $data = $request->validate(RecipeIngredient::updateRules());
            $ingredient = $service->update($recipeIngredient->id, $data);

            return response()->json($ingredient);
        });
    }

    public function destroy(RecipeIngredient $recipeIngredient, DeleteRecipeIngredient $service)
    {
        return $this->execute(function () use ($recipeIngredient, $service) {
            $this->authorize('delete', $recipeIngredient);
            $response = $service->delete($recipeIngredient->id);

            return response()->json($response);
        });
    }
}
