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
use Illuminate\Routing\Controller as BaseController;

class RecipeIngredientController extends BaseController
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    public function index(ListRecipeIngredient $service)
    {
        $ingredients = $service->list();

        return response()->json($ingredients);
    }

    public function store(Request $request, CreateRecipeIngredient $service)
    {
        $this->authorize('create', RecipeIngredient::class);

        $data = $request->validate(RecipeIngredient::createRules());

        $ingredient = $service->create($data);

        return response()->json($ingredient, 201);
    }

    public function show(RecipeIngredient $RecipeIngredient, ShowRecipeIngredient $service)
    {
        $ingredient = $service->show($RecipeIngredient->id);

        return response()->json($ingredient);
    }

    public function update(Request $request, RecipeIngredient $RecipeIngredient, UpdateRecipeIngredient $service)
    {
        $this->authorize("update", $RecipeIngredient);

        $data = $request->validate(RecipeIngredient::updateRules());

        $ingredient = $service->update($RecipeIngredient, $data);

        return response()->json($ingredient);
    }

    public function destroy(RecipeIngredient $RecipeIngredient, DeleteRecipeIngredient $service)
    {
        $this->authorize("delete", $RecipeIngredient);

        $response = $service->delete($RecipeIngredient);

        return response()->json($response);
    }
}
