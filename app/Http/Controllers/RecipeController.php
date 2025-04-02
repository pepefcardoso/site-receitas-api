<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Services\Post\ListFavoriteRecipes;
use App\Services\Post\ListUserRecipes;
use App\Services\Recipe\CreateRecipe;
use App\Services\Recipe\DeleteRecipe;
use App\Services\Recipe\ListFavorites;
use App\Services\Recipe\ListRecipe;
use App\Services\Recipe\ShowRecipe;
use App\Services\Recipe\UpdateRecipe;
use Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class RecipeController extends BaseController
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    public function index(Request $request, ListRecipe $service)
    {
        return $this->execute(function () use ($request, $service) {
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
        });
    }

    public function store(Request $request, CreateRecipe $service)
    {
        return $this->execute(function () use ($request, $service) {
            $this->authorize('create', Recipe::class);
            $data = $request->validate(Recipe::createRules());
            $recipe = $service->create($data);
            return response()->json($recipe, 201);
        });
    }

    public function show(Recipe $recipe, ShowRecipe $service)
    {
        return $this->execute(function () use ($recipe, $service) {
            $recipe = $service->show($recipe->id);
            return response()->json($recipe);
        });
    }

    public function update(Request $request, Recipe $recipe, UpdateRecipe $service)
    {
        return $this->execute(function () use ($request, $recipe, $service) {
            $this->authorize("update", $recipe);
            $data = $request->validate(Recipe::updateRules());
            $recipe = $service->update($recipe->id, $data);
            return response()->json($recipe);
        });
    }

    public function destroy(Recipe $recipe, DeleteRecipe $service)
    {
        return $this->execute(function () use ($recipe, $service) {
            $this->authorize("delete", $recipe);
            $response = $service->delete($recipe);
            return response()->json($response);
        });
    }

    public function userPosts(ListUserRecipes $service)
    {
        return $this->execute(function () use ($service) {
            $perPage = request()->input('per_page', 10);
            $userRecipes = $service->list($perPage);

            return response()->json($userRecipes);
        });
    }

    public function favorites(ListFavoriteRecipes $service)
    {
        return $this->execute(function () use ($service) {
            $perPage = request()->input('per_page', 10);
            $favorites = $service->list($perPage);

            return response()->json($favorites);
        });
    }
}
