<?php

namespace App\Http\Controllers;

use App\Models\RecipeDiet;
use App\Services\RecipeDiets\CreateRecipeDiet;
use App\Services\RecipeDiets\DeleteRecipeDiet;
use App\Services\RecipeDiets\ListRecipeDiet;
use App\Services\RecipeDiets\ShowRecipeDiet;
use App\Services\RecipeDiets\UpdateRecipeDiet;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller as BaseController;

class RecipeDietController extends BaseController
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
        $this->authorizeResource(RecipeDiet::class, 'recipe_diet');
    }

    public function index(ListRecipeDiet $service)
    {
        $diets = $service->list();

        return response()->json($diets);
    }

    public function store(Request $request, CreateRecipeDiet $service)
    {
        $request["normalized_name"] = Str::upper($request->name);
        $data = $request->validate(RecipeDiet::$rules);

        $diet = $service->create($data);

        return response()->json($diet, 201);
    }

    public function show(RecipeDiet $recipeDiet, ShowRecipeDiet $service)
    {
        $diet = $service->show($recipeDiet);

        return response()->json($diet);
    }

    public function update(Request $request, RecipeDiet $recipeDiet, UpdateRecipeDiet $service)
    {
        $request["normalized_name"] = Str::upper($request->name);
        $data = $request->validate(RecipeDiet::$rules);

        $diet = $service->update($recipeDiet, $data);

        return response()->json($diet);
    }

    public function destroy(RecipeDiet $recipeDiet, DeleteRecipeDiet $service)
    {
        $service->delete($recipeDiet);

        return response()->json(null, 204);
    }
}
