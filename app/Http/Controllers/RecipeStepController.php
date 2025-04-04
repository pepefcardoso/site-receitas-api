<?php

namespace App\Http\Controllers;

use App\Models\RecipeStep;
use App\Services\RecipeStep\CreateRecipeStep;
use App\Services\RecipeStep\DeleteRecipeStep;
use App\Services\RecipeStep\ListRecipeStep;
use App\Services\RecipeStep\ShowRecipeStep;
use App\Services\RecipeStep\UpdateRecipeStep;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class RecipeStepController extends BaseController
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    public function index(Request $request, ListRecipeStep $service)
    {
        return $this->execute(function () use ($service) {
            $steps = $service->list();
            return response()->json($steps);
        });
    }

    public function store(Request $request, CreateRecipeStep $service)
    {
        return $this->execute(function () use ($request, $service) {
            $this->authorize('create', RecipeStep::class);

            $data = $request->validate(RecipeStep::createRules());
            $step = $service->create($data);

            return response()->json($step, 201);
        });
    }

    public function show(RecipeStep $recipeStep, ShowRecipeStep $service)
    {
        return $this->execute(function () use ($recipeStep, $service) {
            $step = $service->show($recipeStep->id);
            return response()->json($step);
        });
    }

    public function update(Request $request, RecipeStep $recipeStep, UpdateRecipeStep $service)
    {
        return $this->execute(function () use ($request, $recipeStep, $service) {
            $this->authorize('update', $recipeStep);

            $data = $request->validate(RecipeStep::updateRules());
            $step = $service->update($recipeStep->id, $data);

            return response()->json($step);
        });
    }

    public function destroy(RecipeStep $recipeStep, DeleteRecipeStep $service)
    {
        return $this->execute(function () use ($recipeStep, $service) {
            $this->authorize('delete', $recipeStep);
            $response = $service->delete($recipeStep->id);

            return response()->json($response);
        });
    }
}
