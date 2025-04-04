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

class RecipeDietController extends BaseController
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    public function index(Request $request, ListRecipeDiet $service)
    {
        return $this->execute(function () use ($request, $service) {
            $perPage = $request->input('per_page', 10);
            $diets = $service->list($perPage);
            return response()->json($diets);
        });
    }

    public function store(Request $request, CreateRecipeDiet $service)
    {
        return $this->execute(function () use ($request, $service) {
            $this->authorize('create', RecipeDiet::class);

            $request->merge(['normalized_name' => Str::upper($request->name)]);
            $data = $request->validate(RecipeDiet::createRules());

            $diet = $service->create($data);
            return response()->json($diet, 201);
        });
    }

    public function show(RecipeDiet $recipeDiet, ShowRecipeDiet $service)
    {
        return $this->execute(function () use ($recipeDiet, $service) {
            $diet = $service->show($recipeDiet->id);
            return response()->json($diet);
        });
    }

    public function update(Request $request, RecipeDiet $recipeDiet, UpdateRecipeDiet $service)
    {
        return $this->execute(function () use ($request, $recipeDiet, $service) {
            $this->authorize('update', $recipeDiet);

            $request->merge(['normalized_name' => Str::upper($request->name)]);
            $data = $request->validate(RecipeDiet::updateRules());

            $diet = $service->update($recipeDiet->id, $data);
            return response()->json($diet);
        });
    }

    public function destroy(RecipeDiet $recipeDiet, DeleteRecipeDiet $service)
    {
        return $this->execute(function () use ($recipeDiet, $service) {
            $this->authorize('delete', $recipeDiet);
            $response = $service->delete($recipeDiet->id);
            return response()->json($response);
        });
    }
}
