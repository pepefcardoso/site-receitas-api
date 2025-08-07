<?php

namespace App\Http\Controllers;

use App\Http\Requests\RecipeUnit\StoreRequest;
use App\Http\Requests\RecipeUnit\UpdateRequest;
use App\Http\Resources\RecipeUnit\RecipeUnitResource;
use App\Models\RecipeUnit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

class RecipeUnitController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = $request->input('per_page', 15);
        $orderBy = $request->input('order_by', 'name');
        $orderDirection = $request->input('order_direction', 'asc');
        $page = $request->input('page', 1);

        $cacheKey = "recipe_units_list_{$orderBy}_{$orderDirection}_page_{$page}_per_page_{$perPage}";

        $units = cache()->remember($cacheKey, now()->addHour(), function () use ($orderBy, $orderDirection, $perPage) {
            return RecipeUnit::orderBy($orderBy, $orderDirection)->paginate($perPage);
        });

        return RecipeUnitResource::collection($units);
    }

    public function store(StoreRequest $request): JsonResponse
    {
        $this->authorize('create', RecipeUnit::class);
        $unit = RecipeUnit::create($request->validated());
        Cache::forget('recipe_units_list');
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
        $this->authorize('update', $recipeUnit);
        $recipeUnit->update($request->validated());
        Cache::forget('recipe_units_list');
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
        Cache::forget('recipe_units_list');
        return response()->json(null, 204);
    }
}
