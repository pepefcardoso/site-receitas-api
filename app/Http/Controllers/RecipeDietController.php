<?php

namespace App\Http\Controllers;

use App\Models\RecipeDiet;
use App\Services\RecipeDiets\CreateRecipeUnit;
use App\Services\RecipeDiets\DeleteRecipeUnit;
use App\Services\RecipeDiets\ListRecipeUnit;
use App\Services\RecipeDiets\ShowRecipeUnit;
use App\Services\RecipeDiets\UpdateRecipeUnit;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class RecipeDietController extends Controller
{
    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum', except: ['index', 'show'])
        ];
    }

    public function index(ListRecipeUnit $service)
    {
        $diets = $service->list();

        return response()->json($diets, 201);
    }

    public function store(Request $request, CreateRecipeUnit $service)
    {
        Gate::authorize('isInternalUser');

        $request["normalized_name"] = Str::upper($request->name);
        $data = $request->validate(RecipeDiet::$rules);

        $recipeDiet = $service->create($data);

        return response()->json($recipeDiet, 201);
    }

    public function show(RecipeDiet $RecipeDiet, ShowRecipeUnit $service)
    {
        $recipeDiet = $service->show($RecipeDiet);

        return response()->json($recipeDiet, 201);
    }

    public function update(Request $request, RecipeDiet $recipeDiet, UpdateRecipeUnit $service)
    {
        Gate::authorize('isInternalUser');

        $request["normalized_name"] = Str::upper($request->name);
        $data = $request->validate(RecipeDiet::$rules);

        $recipeDiet = $service->update($recipeDiet, $data);

        return response()->json($recipeDiet, 201);
    }

    public function destroy(RecipeDiet $RecipeDiet, DeleteRecipeUnit $service)
    {
        Gate::authorize('isInternalUser');

        $recipeDiet = $service->delete($RecipeDiet);

        return response()->json($recipeDiet, 201);
    }
}
