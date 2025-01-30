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
use Illuminate\Routing\Controller as BaseController;

class RecipeStepController extends BaseController
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
        $this->authorizeResource(RecipeStep::class, 'recipe_step');
    }

    public function index(ListRecipeStep $service)
    {
        $steps = $service->list();

        return response()->json($steps);
    }

    public function store(Request $request, CreateRecipeStep $service)
    {
        $data = $request->validate(RecipeStep::createRules());

        $step = $service->create($data);

        return response()->json($step, 201);
    }

    public function show(RecipeStep $RecipeStep, ShowRecipeStep $service)
    {
        $step = $service->show($RecipeStep);

        return response()->json($step);
    }

    public function update(Request $request, RecipeStep $RecipeStep, UpdateRecipeStep $service)
    {
        $data = $request->validate(RecipeStep::createRules());

        $step = $service->update($RecipeStep, $data);

        return response()->json($step);
    }

    public function destroy(RecipeStep $RecipeStep, DeleteRecipeStep $service)
    {
        $service->delete($RecipeStep);

        return response()->json(null, 204);
    }
}
