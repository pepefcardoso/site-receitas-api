<?php

namespace App\Http\Controllers;

use App\Models\RecipeStep;
use App\Services\RecipeStep\CreateRecipeStep;
use App\Services\RecipeStep\DeleteRecipeStep;
use App\Services\RecipeStep\ListRecipeStep;
use App\Services\RecipeStep\ShowRecipeStep;
use App\Services\RecipeStep\UpdateRecipeStep;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;

class RecipeStepController extends Controller
{
    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum', except: ['index', 'show'])
        ];
    }

    public function index(ListRecipeStep $service)
    {
        $steps = $service->list();

        return response()->json($steps, 201);
    }

    public function store(Request $request, CreateRecipeStep $service)
    {
        Gate::authorize('modify');

        $data = $request->validate(RecipeStep::createRules());

        $recipeStep = $service->create($data);

        return response()->json($recipeStep, 201);
    }

    public function show(RecipeStep $RecipeStep, ShowRecipeStep $service)
    {
        $RecipeStep = $service->show($RecipeStep);

        return response()->json($RecipeStep, 201);
    }

    public function update(Request $request, RecipeStep $RecipeStep, UpdateRecipeStep $service)
    {
        Gate::authorize('modify');

        $data = $request->validate(RecipeStep::createRules());

        $RecipeStep = $service->update($RecipeStep, $data);

        return response()->json($RecipeStep, 201);
    }

    public function destroy(RecipeStep $RecipeStep, DeleteRecipeStep $service)
    {
        Gate::authorize('modify');

        $service->delete($RecipeStep);

        return response()->json(null, 204);
    }
}
