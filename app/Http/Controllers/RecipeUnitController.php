<?php

namespace App\Http\Controllers;

use App\Models\RecipeUnit;
use App\Services\RecipeUnit\CreateRecipeUnit;
use App\Services\RecipeUnit\DeleteRecipeUnit;
use App\Services\RecipeUnit\ListRecipeUnit;
use App\Services\RecipeUnit\ShowRecipeUnit;
use App\Services\RecipeUnit\UpdateRecipeUnit;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class RecipeUnitController extends Controller
{
    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum', except: ['index', 'show'])
        ];
    }

    public function index(ListRecipeUnit $service)
    {
        $units = $service->list();

        return response()->json($units, 201);
    }

    public function store(Request $request, CreateRecipeUnit $service)
    {
        Gate::authorize('isInternalUser');

        $request["normalized_name"] = Str::upper($request->name);
        $data = $request->validate(RecipeUnit::$rules);

        $RecipeUnit = $service->create($data);

        return response()->json($RecipeUnit, 201);
    }

    public function show(RecipeUnit $recipeUnit, ShowRecipeUnit $service)
    {
        $recipeUnit = $service->show($recipeUnit);

        return response()->json($recipeUnit, 201);
    }

    public function update(Request $request, RecipeUnit $recipeUnit, UpdateRecipeUnit $service)
    {
        Gate::authorize('isInternalUser');

        $request["normalized_name"] = Str::upper($request->name);
        $data = $request->validate(RecipeUnit::$rules);

        $recipeUnit = $service->update($recipeUnit, $data);

        return response()->json($recipeUnit, 201);
    }

    public function destroy(RecipeUnit $recipeUnit, DeleteRecipeUnit $service)
    {
        Gate::authorize('isInternalUser');

        $recipeUnit = $service->delete($recipeUnit);

        return response()->json(null, 204);
    }
}
