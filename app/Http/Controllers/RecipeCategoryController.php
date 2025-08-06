<?php

namespace App\Http\Controllers;

use App\Http\Requests\RecipeCategory\StoreRequest;
use App\Http\Requests\RecipeCategory\UpdateRequest;
use App\Http\Resources\RecipeCategory\RecipeCategoryResource;
use App\Models\RecipeCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

class RecipeCategoryController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    public function index(): AnonymousResourceCollection
    {
        $categories = cache()->remember('recipe_categories_list', now()->addHour(), function () {
            return RecipeCategory::all();
        });
        return RecipeCategoryResource::collection($categories);
    }

    public function store(StoreRequest $request): JsonResponse
    {
        $this->authorize('create', RecipeCategory::class);
        $category = RecipeCategory::create($request->validated());
        Cache::forget('recipe_categories_list');
        return (new RecipeCategoryResource($category))
            ->response()
            ->setStatusCode(201);
    }

    public function show(RecipeCategory $recipeCategory): RecipeCategoryResource
    {
        return new RecipeCategoryResource($recipeCategory);
    }

    public function update(UpdateRequest $request, RecipeCategory $recipeCategory): RecipeCategoryResource
    {
        $this->authorize('update', $recipeCategory);
        $recipeCategory->update($request->validated());
        Cache::forget('recipe_categories_list');
        return new RecipeCategoryResource($recipeCategory);
    }

    public function destroy(RecipeCategory $recipeCategory): JsonResponse
    {
        $this->authorize('delete', $recipeCategory);
        if ($recipeCategory->recipes()->exists()) {
            throw ValidationException::withMessages([
                'category' => 'This category cannot be deleted because it is associated with recipes.',
            ]);
        }
        $recipeCategory->delete();
        Cache::forget('recipe_categories_list');
        return response()->json(null, 204);
    }
}
