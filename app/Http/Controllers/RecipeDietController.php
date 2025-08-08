<?php

namespace App\Http\Controllers;

use App\Http\Requests\RecipeDiet\StoreRequest;
use App\Http\Requests\RecipeDiet\UpdateRequest;
use App\Http\Resources\RecipeDiet\RecipeDietResource;
use App\Models\RecipeDiet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;

class RecipeDietController extends BaseController
{
    protected string $cacheIndexKey = 'recipe_diets_index';

    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = $request->input('per_page', 15);
        $orderBy = $request->input('order_by', 'name');
        $orderDirection = $request->input('order_direction', 'asc');
        $page = $request->input('page', 1);
        $search = $request->input('search', '');

        $cacheKey = "recipe_diets_list_{$orderBy}_{$orderDirection}_page_{$page}_per_page_{$perPage}_search_{$search}";

        $diets = Cache::remember(
            $cacheKey,
            now()->addHour(),
            function () use ($orderBy, $orderDirection, $perPage, $search) {
                $query = RecipeDiet::query();

                if (!empty($search)) {
                    $query->where('name', 'like', "%{$search}%");
                }

                return $query->orderBy($orderBy, $orderDirection)->paginate($perPage);
            }
        );

        $this->addToCacheIndex($cacheKey);

        return RecipeDietResource::collection($diets);
    }

    public function store(StoreRequest $request): JsonResponse
    {
        $this->authorize('create', RecipeDiet::class);
        $diet = RecipeDiet::create($request->validated());
        $this->flushCacheIndex();

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
        $this->flushCacheIndex();

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
        $this->flushCacheIndex();

        return response()->json(null, 204);
    }

    private function addToCacheIndex(string $key): void
    {
        $index = Cache::get($this->cacheIndexKey, []);
        $index[$key] = true;
        Cache::put($this->cacheIndexKey, $index, now()->addHour());
    }

    private function flushCacheIndex(): void
    {
        $index = Cache::get($this->cacheIndexKey, []);
        foreach (array_keys($index) as $key) {
            Cache::forget($key);
        }
        Cache::forget($this->cacheIndexKey);
    }
}
