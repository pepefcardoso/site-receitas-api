<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ManagesResourceCaching;
use App\Http\Requests\RecipeUnit\StoreRequest;
use App\Http\Requests\RecipeUnit\UpdateRequest;
use App\Http\Resources\RecipeUnit\RecipeUnitResource;
use App\Models\RecipeUnit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\ValidationException;

class RecipeUnitController extends BaseController
{
    use ManagesResourceCaching;

    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    protected function getCacheTag(): string
    {
        return 'recipe_units';
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $units = $this->getCachedAndPaginated($request, RecipeUnit::query());

        return RecipeUnitResource::collection($units);
    }

    public function store(StoreRequest $request): JsonResponse
    {
        $unit = RecipeUnit::create($request->validated());

        $this->flushResourceCache();

        return (new RecipeUnitResource($unit))
            ->response()
            ->setStatusCode(201);
    }

    public function show(RecipeUnit $recipeUnit): RecipeUnitResource
    {
        return new RecipeUnitResource($recipeUnit);
    }

    public function update(UpdateRequest $request, RecipeUnit $recipeUnit): RecipeUnitResource
    {
        $recipeUnit->update($request->validated());

        $this->flushResourceCache();

        return new RecipeUnitResource($recipeUnit);
    }

    public function destroy(RecipeUnit $recipeUnit): JsonResponse
    {
        $this->authorize('delete', $recipeUnit);

        if ($recipeUnit->ingredients()->exists()) {
            throw ValidationException::withMessages([
                'unit' => 'This unit cannot be deleted because it is associated with one or more ingredients.',
            ]);
        }

        $recipeUnit->delete();

        $this->flushResourceCache();

        return response()->json(null, 204);
    }
}
