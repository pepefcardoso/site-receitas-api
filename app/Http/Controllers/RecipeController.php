<?php

namespace App\Http\Controllers;

use App\Http\Requests\Recipe\FilterRecipeRequest;
use App\Http\Requests\Recipe\StoreRecipeRequest;
use App\Http\Requests\Recipe\UpdateRecipeRequest;
use App\Http\Resources\Recipe\RecipeCollectionResource;
use App\Http\Resources\Recipe\RecipeResource;
use App\Models\Recipe;
use App\Services\Recipe\CreateRecipe;
use App\Services\Recipe\DeleteRecipe;
use App\Services\Recipe\ListFavoriteRecipes;
use App\Services\Recipe\ListRecipe;
use App\Services\Recipe\ListUserRecipes;
use App\Services\Recipe\ShowRecipe;
use App\Services\Recipe\UpdateRecipe;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RecipeController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    public function index(FilterRecipeRequest $request, ListRecipe $service): AnonymousResourceCollection
    {
        $filters = $request->validated();
        $perPage = $filters['per_page'] ?? 10;

        $recipes = $service->list($filters, $perPage);

        return RecipeCollectionResource::collection($recipes);
    }

    public function store(StoreRecipeRequest $request, CreateRecipe $service): RecipeResource
    {
        $recipe = $service->create($request->validated());

        $recipe->load(['user.image', 'category', 'diets', 'ingredients.unit', 'steps', 'image']);

        return new RecipeResource($recipe);
    }

    public function show(Recipe $recipe, ShowRecipe $service): RecipeResource
    {
        $detailedRecipe = $service->show($recipe->id);
        return new RecipeResource($detailedRecipe);
    }

    public function update(UpdateRecipeRequest $request, Recipe $recipe, UpdateRecipe $service): RecipeResource
    {
        $updatedRecipe = $service->update($recipe->id, $request->validated());

        $updatedRecipe->load(['user.image', 'category', 'diets', 'ingredients.unit', 'steps', 'image']);

        return new RecipeResource($updatedRecipe);
    }

    public function destroy(Recipe $recipe, DeleteRecipe $service)
    {
        $this->authorize("delete", $recipe);
        $service->delete($recipe->id);

        return response()->json(null, 204);
    }

    public function userRecipes(ListUserRecipes $service): AnonymousResourceCollection
    {
        $perPage = request()->input('per_page', 10);
        $userRecipes = $service->list($perPage);
        return RecipeCollectionResource::collection($userRecipes);
    }

    public function favorites(ListFavoriteRecipes $service): AnonymousResourceCollection
    {
        $perPage = request()->input('per_page', 10);
        $favorites = $service->list($perPage);
        return RecipeCollectionResource::collection($favorites);
    }
}
