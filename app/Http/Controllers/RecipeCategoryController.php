<?php

namespace App\Http\Controllers;

use App\Models\RecipeCategory;
use App\Services\RecipeCategory\CreateRecipeCategory;
use App\Services\RecipeCategory\DeleteRecipeCategory;
use App\Services\RecipeCategory\ListRecipeCategory;
use App\Services\RecipeCategory\ShowRecipeCategory;
use App\Services\RecipeCategory\UpdateRecipeCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class RecipeCategoryController extends BaseController
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    public function index(Request $request, ListRecipeCategory $service)
    {
        return $this->execute(function () use ($request, $service) {
            $perPage = $request->input('per_page', 10);
            $categories = $service->list($perPage);
            return response()->json($categories);
        });
    }

    public function store(Request $request, CreateRecipeCategory $service)
    {
        return $this->execute(function () use ($request, $service) {
            $this->authorize('create', RecipeCategory::class);

            $request->merge(['normalized_name' => Str::upper($request->name)]);
            $data = $request->validate(RecipeCategory::createRules());

            $category = $service->create($data);
            return response()->json($category, 201);
        });
    }

    public function show(RecipeCategory $recipeCategory, ShowRecipeCategory $service)
    {
        return $this->execute(function () use ($recipeCategory, $service) {
            $category = $service->show($recipeCategory->id);
            return response()->json($category);
        });
    }

    public function update(Request $request, RecipeCategory $recipeCategory, UpdateRecipeCategory $service)
    {
        return $this->execute(function () use ($request, $recipeCategory, $service) {
            $this->authorize("update", $recipeCategory);

            $request->merge(['normalized_name' => Str::upper($request->name)]);
            $data = $request->validate(RecipeCategory::updateRules());

            $category = $service->update($recipeCategory, $data);
            return response()->json($category);
        });
    }

    public function destroy(RecipeCategory $recipeCategory, DeleteRecipeCategory $service)
    {
        return $this->execute(function () use ($recipeCategory, $service) {
            $this->authorize("delete", $recipeCategory);
            $response = $service->delete($recipeCategory);
            return response()->json($response);
        });
    }
}
