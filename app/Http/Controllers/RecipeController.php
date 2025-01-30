<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Services\Recipe\CreateRecipe;
use App\Services\Recipe\DeleteRecipe;
use App\Services\Recipe\ListRecipe;
use App\Services\Recipe\ShowRecipe;
use App\Services\Recipe\UpdateRecipe;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class RecipeController extends BaseController
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    public function index(ListRecipe $service)
    {
        $recipes = $service->list();

        return response()->json($recipes);
    }

    public function store(Request $request, CreateRecipe $service)
    {
        $this->authorize('create', Recipe::class);

        $data = $request->validate(Recipe::createRules());

        $recipe = $service->create($data);

        return response()->json($recipe, 201);
    }

    public function show(Recipe $recipe, ShowRecipe $service)
    {
        $recipe = $service->show($recipe);

        return response()->json($recipe);
    }

    public function update(Request $request, Recipe $recipe, UpdateRecipe $service)
    {
        $this->authorize("update", $recipe);

        $data = $request->validate(Recipe::updateRules());

        $recipe = $service->update($recipe->id, $data);

        return response()->json($recipe);
    }

    public function destroy(Recipe $recipe, DeleteRecipe $service)
    {
        $this->authorize("delete", $recipe);

        $service->delete($recipe);

        return response()->json(null, 204);
    }
}
