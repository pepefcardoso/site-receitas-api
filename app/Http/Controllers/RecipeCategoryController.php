<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ManagesResourceCaching;
use App\Http\Requests\RecipeCategory\StoreRecipeCategoryRequest;
use App\Http\Requests\RecipeCategory\UpdateRecipeCategoryRequest;
use App\Http\Resources\RecipeCategory\RecipeCategoryResource;
use App\Models\RecipeCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\ValidationException;

class RecipeCategoryController extends BaseController
{
    use ManagesResourceCaching;

    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    protected function getCacheTag(): string
    {
        return 'recipe_categories';
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', RecipeCategory::class);

        $categories = $this->getCachedAndPaginated($request, RecipeCategory::query());

        return RecipeCategoryResource::collection($categories);
    }

    public function store(StoreRecipeCategoryRequest $request): JsonResponse
    {
        $category = RecipeCategory::create($request->validated());

        $this->flushResourceCache();

        return (new RecipeCategoryResource($category))
            ->response()
            ->setStatusCode(201);
    }

    public function show(RecipeCategory $recipeCategory): RecipeCategoryResource
    {
        $this->authorize('view', $recipeCategory);

        return new RecipeCategoryResource($recipeCategory);
    }

    public function update(UpdateRecipeCategoryRequest $request, RecipeCategory $recipeCategory): RecipeCategoryResource
    {
        $recipeCategory->update($request->validated());

        $this->flushResourceCache();

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

        $this->flushResourceCache();

        return response()->json(null, 204);
    }
}
