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
use Illuminate\Routing\Controller as BaseController;

class RecipeCategoryController extends BaseController
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    public function index(ListRecipeCategory $service)
    {
        $categories = $service->list();

        return response()->json($categories);
    }

    public function store(Request $request, CreateRecipeCategory $service)
    {
        $this->authorize('create', RecipeCategory::class);

        $request["normalized_name"] = Str::upper($request->name);
        $data = $request->validate(RecipeCategory::rules());

        $category = $service->create($data);

        return response()->json($category, 201);
    }

    public function show(RecipeCategory $RecipeCategory, ShowRecipeCategory $service)
    {
        $category = $service->show($RecipeCategory->id);

        return response()->json($category);
    }

    public function update(Request $request, RecipeCategory $RecipeCategory, UpdateRecipeCategory $service)
    {
        $this->authorize("update", $RecipeCategory);

        $request["normalized_name"] = Str::upper($request->name);
        $data = $request->validate(RecipeCategory::rules());

        $category = $service->update($RecipeCategory, $data);

        return response()->json($category);
    }

    public function destroy(RecipeCategory $RecipeCategory, DeleteRecipeCategory $service)
    {
        $this->authorize("delete", $RecipeCategory);

        $response = $service->delete($RecipeCategory);

        return response()->json($response);
    }
}
