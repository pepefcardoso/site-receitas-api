<?php

namespace App\Http\Controllers;

use App\Http\Requests\Rating\StoreRatingRequest;
use App\Http\Requests\Rating\UpdateRatingRequest;
use App\Http\Resources\Rating\RatingResource;
use App\Models\Rating;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class RatingController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show', 'showUserRating']);
    }

    public function index(Request $request, string $type, int $rateableId): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Rating::class);

        $rateable = $this->resolveRateable($type, $rateableId);
        $cacheTags = $this->getCacheTagsForRateable($rateable);

        $queryParams = $request->query();
        $cacheKey = "{$cacheTags[0]}:list:" . http_build_query($queryParams);

        $ratings = Cache::tags($cacheTags)->remember($cacheKey, now()->addHour(), function () use ($request, $rateable) {
            $perPage = $request->input('per_page', 15);
            $orderBy = $request->input('order_by', 'created_at');
            $orderDirection = $request->input('order_direction', 'desc');

            return $rateable->ratings()
                ->with('user')
                ->orderBy($orderBy, $orderDirection)
                ->paginate($perPage);
        });

        return RatingResource::collection($ratings);
    }

    public function store(StoreRatingRequest $request, string $type, int $rateableId): JsonResponse
    {
        $rateable = $this->resolveRateable($type, $rateableId);

        $rating = $rateable->ratings()->updateOrCreate(
            ['user_id' => Auth::id()],
            ['rating' => $request->validated('rating')]
        );

        $this->flushRateableCache($rateable);

        $statusCode = $rating->wasRecentlyCreated ? 201 : 200;
        return (new RatingResource($rating))->response()->setStatusCode($statusCode);
    }

    public function update(UpdateRatingRequest $request, Rating $rating): RatingResource
    {
        $rating->update($request->validated());

        $this->flushRateableCache($rating->rateable);

        return new RatingResource($rating);
    }

    public function destroy(Rating $rating): JsonResponse
    {
        $this->authorize('delete', $rating);

        $rateable = $rating->rateable;
        $rating->delete();

        $this->flushRateableCache($rateable);

        return response()->json(null, 204);
    }

    public function showUserRating(Request $request, string $type, int $rateableId)
    {
        $this->authorize('view', Rating::class);

        $rateable = $this->resolveRateable($type, $rateableId);
        $rating = $rateable->ratings()->where('user_id', $request->user()->id)->first();

        if (!$rating) {
            return response()->json(['message' => 'Nenhuma avaliação encontrada para este usuário.'], 404);
        }
        return new RatingResource($rating);
    }

    public function show(Rating $rating): RatingResource
    {
        $this->authorize('view', $rating);

        return new RatingResource($rating);
    }

    protected function resolveRateable(string $type, $id)
    {
        $class = 'App\\Models\\' . Str::studly(Str::singular($type));
        abort_unless(class_exists($class), 404, "Tipo inválido: {$type}");
        return $class::findOrFail($id);
    }

    private function getCacheTagsForRateable(Model $rateable): array
    {
        $type = $rateable->getMorphClass();
        return ["ratings:{$type}:{$rateable->id}", 'ratings'];
    }

    private function flushRateableCache(Model $rateable): void
    {
        Cache::tags($this->getCacheTagsForRateable($rateable))->flush();
    }
}
