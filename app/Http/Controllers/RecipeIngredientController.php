<?php

namespace App\Http\Controllers;

use App\Models\RecipeIngredient;
use App\Services\RecipeIngredient\CreateRecipeIngredient;
use App\Services\RecipeIngredient\DeleteRecipeIngredient;
use App\Services\RecipeIngredient\ListRecipeIngredient;
use App\Services\RecipeIngredient\ShowRecipeIngredient;
use App\Services\RecipeIngredient\UpdateRecipeIngredient;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;

class RecipeIngredientController extends Controller
{
    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum', except: ['index', 'show'])
        ];
    }

    public function index(ListRecipeIngredient $service)
    {
        $ingredients = $service->list();

        return response()->json($ingredients, 201);
    }

    public function store(Request $request, CreateRecipeIngredient $service)
    {
        $data = $request->validate(RecipeIngredient::rules());

        $RecipeIngredient = $service->create($data);

        return response()->json($RecipeIngredient, 201);
    }

    public function show(RecipeIngredient $RecipeIngredient, ShowRecipeIngredient $service)
    {
        $RecipeIngredient = $service->show($RecipeIngredient);

        return response()->json($RecipeIngredient, 201);
    }

    public function update(Request $request, RecipeIngredient $RecipeIngredient, UpdateRecipeIngredient $service)
    {
        Gate::authorize('modify', $RecipeIngredient);

        $data = $request->validate(RecipeIngredient::rules());

        $RecipeIngredient = $service->update($RecipeIngredient, $data);

        return response()->json($RecipeIngredient, 201);
    }

    public function destroy(RecipeIngredient $RecipeIngredient, DeleteRecipeIngredient $service)
    {
        Gate::authorize('modify', $RecipeIngredient);

        $service->delete($RecipeIngredient);

        return response()->json(null, 204);
    }
}
