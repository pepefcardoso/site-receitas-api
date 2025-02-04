<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Services\Recipe\CreateRecipe;
use App\Services\Recipe\DeleteRecipe;
use App\Services\Recipe\ListRecipe;
use App\Services\Recipe\ShowRecipe;
use App\Services\Recipe\UpdateRecipe;
use Auth;
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

    public function index(Request $request, ListRecipe $service)
    {
        $validatedFilters = $request->validate(Recipe::filtersRules());

        $filters = [
            'title' => $validatedFilters['title'] ?? null,
            'category_id' => $validatedFilters['category_id'] ?? null,
            'diets' => $validatedFilters['diets'] ?? null,
            'order_by' => $validatedFilters['order_by'] ?? 'created_at',
            'order_direction' => $validatedFilters['order_direction'] ?? 'desc',
            'user_id' => $validatedFilters['user_id'] ?? null,
        ];

        $perPage = $validatedFilters['per_page'] ?? 10;

        $recipes = $service->list($filters, $perPage);

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
        $recipe = $service->show($recipe->id);

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

        $response = $service->delete($recipe);

        return response()->json($response);
    }

    public function userRecipes(ListRecipe $service)
    {
        $filters = [
            'title' => request()->input('title'),
            'category_id' => request()->input('category_id'),
            'diets' => request()->input('diets'),
            'order_by' => request()->input('order_by', 'created_at'),
            'order_direction' => request()->input('order_direction', 'desc'),
            'user_id' => Auth::id()
        ];

        $perPage = request()->input('per_page', 10);

        $recipes = $service->list($filters, $perPage);

        return response()->json($recipes);
    }
}
