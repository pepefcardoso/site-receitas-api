<?php

namespace App\Http\Controllers;

use App\Models\RecipeDiet;
use App\Services\RecipeDiets\CreateRecipeDiet;
use App\Services\RecipeDiets\DeleteRecipeDiet;
use App\Services\RecipeDiets\ListRecipeDiet;
use App\Services\RecipeDiets\ShowRecipeDiet;
use App\Services\RecipeDiets\UpdateRecipeDiet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
        return response()->json($service->list(), 200);
    }

    public function store(Request $request, CreateRecipeDiet $service)
    {
        if (config('app.debug')) {
            Log::info('Authenticated user:', ['user' => auth()->user()]);
        }

        $request["normalized_name"] = Str::upper($request->name);
        $data = $request->validate(RecipeDiet::$rules);

        return response()->json($service->create($data), 201);
    }

    public function show(RecipeDiet $recipeDiet, ShowRecipeDiet $service)
    {
        return response()->json($service->show($recipeDiet), 200);
    }

    public function update(Request $request, RecipeDiet $recipeDiet, UpdateRecipeDiet $service)
    {
        $request["normalized_name"] = Str::upper($request->name);
        $data = $request->validate(RecipeDiet::$rules);

        return response()->json($service->update($recipeDiet, $data), 200);
    }

    public function destroy(RecipeDiet $recipeDiet, DeleteRecipeDiet $service)
    {
        $service->delete($recipeDiet);
        return response()->json(null, 204);
    }
}
