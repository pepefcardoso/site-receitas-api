<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Services\Recipe\CreateRecipe;
use App\Services\Recipe\DeleteRecipe;
use App\Services\Recipe\ListRecipe;
use App\Services\Recipe\ShowRecipe;
use App\Services\Recipe\UpdateRecipe;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;

class RecipeController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum', except: ['index', 'show'])
        ];
    }

    public function index(ListRecipe $service)
    {
        $recipes = $service->list();

        return response()->json($recipes, 201);
    }

    public function store(Request $request, CreateRecipe $service)
    {
        $fields = $request->validate(Recipe::createRules());

        $recipe = $service->create($fields);

        return response()->json($recipe, 201);
    }

    public function show(Recipe $recipe, ShowRecipe $service)
    {
        $recipe = $service->show($recipe);

        return response()->json($recipe, 200);
    }

    public function update(Recipe $recipe, UpdateRecipe $service)
    {
        Gate::authorize('modify', $recipe);

        $fields = request()->validate(Recipe::updateRules());

        $updatedRecipe = $service->update($recipe->id, $fields);

        return response()->json($updatedRecipe, 201);
    }

    public function destroy(Recipe $recipe, DeleteRecipe $service)
    {
        Gate::authorize('modify', $recipe);

        $service->delete($recipe);

        return response()->json(null, 204);
    }
}
