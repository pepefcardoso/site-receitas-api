<?php

namespace App\Http\Controllers;

use App\Http\Requests\RecipeUnit\StoreRequest;
use App\Http\Requests\RecipeUnit\UpdateRequest;
use App\Http\Resources\RecipeUnitResource;
use App\Models\RecipeUnit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;

class RecipeUnitController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    public function index(): AnonymousResourceCollection
    {
        $units = cache()->remember('recipe_units_list', now()->addHour(), function () {
            return RecipeUnit::all();
        });
        return RecipeUnitResource::collection($units);
    }

    public function store(StoreRequest $request): JsonResponse
    {
        $unit = RecipeUnit::create($request->validated());
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
        return response()->json(null, 204);
    }
}
