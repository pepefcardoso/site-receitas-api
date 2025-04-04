<?php

namespace App\Http\Controllers;

use App\Models\RecipeUnit;
use App\Services\RecipeUnit\CreateRecipeUnit;
use App\Services\RecipeUnit\DeleteRecipeUnit;
use App\Services\RecipeUnit\ListRecipeUnit;
use App\Services\RecipeUnit\ShowRecipeUnit;
use App\Services\RecipeUnit\UpdateRecipeUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class RecipeUnitController extends BaseController
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    public function index(Request $request, ListRecipeUnit $service)
    {
        return $this->execute(function () use ($request, $service) {
            $perPage = $request->input('per_page', 10);
            $units = $service->list($perPage);
            return response()->json($units);
        });
    }

    public function store(Request $request, CreateRecipeUnit $service)
    {
        return $this->execute(function () use ($request, $service) {
            $this->authorize('create', RecipeUnit::class);

            $request->merge(['normalized_name' => Str::upper($request->name)]);
            $data = $request->validate(RecipeUnit::rules());

            $unit = $service->create($data);
            return response()->json($unit, 201);
        });
    }

    public function show(RecipeUnit $recipeUnit, ShowRecipeUnit $service)
    {
        return $this->execute(function () use ($recipeUnit, $service) {
            $unit = $service->show($recipeUnit->id);
            return response()->json($unit);
        });
    }

    public function update(Request $request, RecipeUnit $recipeUnit, UpdateRecipeUnit $service)
    {
        return $this->execute(function () use ($request, $recipeUnit, $service) {
            $this->authorize('update', $recipeUnit);

            $request->merge(['normalized_name' => Str::upper($request->name)]);
            $data = $request->validate(RecipeUnit::rules());

            $unit = $service->update($recipeUnit->id, $data);
            return response()->json($unit);
        });
    }

    public function destroy(RecipeUnit $recipeUnit, DeleteRecipeUnit $service)
    {
        return $this->execute(function () use ($recipeUnit, $service) {
            $this->authorize('delete', $recipeUnit);
            $response = $service->delete($recipeUnit->id);
            return response()->json($response);
        });
    }
}
