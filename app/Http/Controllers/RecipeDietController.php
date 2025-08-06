<?php

namespace App\Http\Controllers;

use App\Http\Requests\RecipeDiet\StoreRequest;
use App\Http\Requests\RecipeDiet\UpdateRequest;
use App\Http\Resources\RecipeDiet\RecipeDietResource;
use App\Models\RecipeDiet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

class RecipeDietController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    public function index(): AnonymousResourceCollection
    {
        $diets = cache()->remember('recipe_diets_list', now()->addHour(), function () {
            return RecipeDiet::all();
        });
        return RecipeDietResource::collection($diets);
    }

    public function store(StoreRequest $request): JsonResponse
    {
        $this->authorize('create', RecipeDiet::class);
        $diet = RecipeDiet::create($request->validated());
        Cache::forget('recipe_diets_list');
        return (new RecipeDietResource($diet))
            ->response()
            ->setStatusCode(201);
    }

    public function show(RecipeDiet $recipeDiet): RecipeDietResource
    {
        return new RecipeDietResource($recipeDiet);
    }

    public function update(UpdateRequest $request, RecipeDiet $recipeDiet): RecipeDietResource
    {
        $this->authorize('update', $recipeDiet);
        $recipeDiet->update($request->validated());
        Cache::forget('recipe_diets_list');
        return new RecipeDietResource($recipeDiet);
    }

    public function destroy(RecipeDiet $recipeDiet): JsonResponse
    {
        $this->authorize('delete', $recipeDiet);
        if ($recipeDiet->recipes()->exists()) {
            throw ValidationException::withMessages([
                'diet' => 'This diet cannot be deleted because it is associated with recipes.',
            ]);
        }
        $recipeDiet->delete();
        Cache::forget('recipe_diets_list');
        return response()->json(null, 204);
    }
}
