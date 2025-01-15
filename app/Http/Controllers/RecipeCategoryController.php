<?php

namespace App\Http\Controllers;

use App\Models\RecipeCategory;
use App\Services\RecipeCategory\CreateRecipeCategory;
use App\Services\RecipeCategory\DeleteRecipeCategory;
use App\Services\RecipeCategory\ListRecipeCategory;
use App\Services\RecipeCategory\ShowRecipeCategory;
use App\Services\RecipeCategory\UpdateRecipeCategory;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class RecipeCategoryController extends Controller
{
    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum', except: ['index', 'show'])
        ];
    }

    public function index(ListRecipeCategory $service)
    {
        $categories = $service->list();

        return response()->json($categories, 201);
    }

    public function store(Request $request, CreateRecipeCategory $service)
    {
        Gate::authorize('isInternalUser');

        $request["normalized_name"] = Str::upper($request->name);
        $data = $request->validate(RecipeCategory::$rules);

        $recipeCategory = $service->create($data);

        return response()->json($recipeCategory, 201);
    }

    public function show(RecipeCategory $RecipeCategory, ShowRecipeCategory $service)
    {
        $RecipeCategory = $service->show($RecipeCategory);

        return response()->json($RecipeCategory, 201);
    }

    public function update(Request $request, RecipeCategory $RecipeCategory, UpdateRecipeCategory $service)
    {
        Gate::authorize('isInternalUser');

        $request["normalized_name"] = Str::upper($request->name);
        $data = $request->validate(RecipeCategory::$rules);

        $RecipeCategory = $service->update($RecipeCategory, $data);

        return response()->json($RecipeCategory, 201);
    }

    public function destroy(RecipeCategory $RecipeCategory, DeleteRecipeCategory $service)
    {
        Gate::authorize('isInternalUser');

        $RecipeCategory = $service->delete($RecipeCategory);

        return response()->json($RecipeCategory, 201);
    }
}
