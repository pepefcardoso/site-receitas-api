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
use Illuminate\Routing\Controller as BaseController;

class RecipeUnitController extends BaseController
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    public function index(ListRecipeUnit $service)
    {
        $units = $service->list();

        return response()->json($units);
    }

    public function store(Request $request, CreateRecipeUnit $service)
    {
        $this->authorize('create', RecipeUnit::class);

        $request["normalized_name"] = Str::upper($request->name);
        $data = $request->validate(RecipeUnit::rules());

        $unit = $service->create($data);

        return response()->json($unit, 201);
    }

    public function show(RecipeUnit $recipeUnit, ShowRecipeUnit $service)
    {
        $unit = $service->show($recipeUnit->id);

        return response()->json($unit);
    }

    public function update(Request $request, RecipeUnit $recipeUnit, UpdateRecipeUnit $service)
    {
        $this->authorize("update", $recipeUnit);

        $request["normalized_name"] = Str::upper($request->name);
        $data = $request->validate(RecipeUnit::rules());

        $unit = $service->update($recipeUnit, $data);

        return response()->json($unit);
    }

    public function destroy(RecipeUnit $recipeUnit, DeleteRecipeUnit $service)
    {
        $this->authorize("delete", $recipeUnit);

        $response = $service->delete($recipeUnit);

        return response()->json($response);
    }
}
