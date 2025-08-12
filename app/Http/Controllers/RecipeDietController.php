<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ManagesResourceCaching;
use App\Http\Requests\RecipeDiet\StoreRequest;
use App\Http\Requests\RecipeDiet\UpdateRequest;
use App\Http\Resources\RecipeDiet\RecipeDietResource;
use App\Models\RecipeDiet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\ValidationException;

class RecipeDietController extends BaseController
{
    use ManagesResourceCaching;

    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    protected function getCacheTag(): string
    {
        return 'recipe_diets';
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $diets = $this->getCachedAndPaginated($request, RecipeDiet::query());

        return RecipeDietResource::collection($diets);
    }

    public function store(StoreRequest $request): JsonResponse
    {
        $diet = RecipeDiet::create($request->validated());

        $this->flushResourceCache();

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
        $recipeDiet->update($request->validated());

        $this->flushResourceCache();

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

        $this->flushResourceCache();

        return response()->json(null, 204);
    }
}
