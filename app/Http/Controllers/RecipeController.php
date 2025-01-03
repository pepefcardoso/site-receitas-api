<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRecipeRequest;
use App\Http\Requests\UpdateRecipeRequest;
use App\Models\Recipe;
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

    public function index()
    {
        $recipes = Recipe::with(['user'])->get();
        return response()->json($recipes, 201);
    }

    public function store(Request $request)
    {
        $fields = request()->validate(Recipe::rules());

        $recipe = $request->user()->recipes()->create($fields);

        if ($request->has('diets')) {
            $recipe->diets()->sync($request->diets);
        }

        return response()->json($recipe, 201);
    }

    public function show(Recipe $recipe)
    {
        return response()->json($recipe->load('user'), 201);
    }

    public function update(Recipe $recipe)
    {
        Gate::authorize('modify', $recipe);

        $fields = request()->validate(Recipe::rules());

        $recipe->update($fields);

        if (request()->has('diets')) {
            $recipe->diets()->sync(request()->diets);
        }

        return response()->json($recipe, 201);
    }

    public function destroy(Recipe $recipe)
    {
        Gate::authorize('modify', $recipe);

        $recipe->delete();

        return response()->json(null, 204);
    }
}
