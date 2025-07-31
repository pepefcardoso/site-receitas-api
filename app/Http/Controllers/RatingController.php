<?php

namespace App\Http\Controllers;

use App\Http\Requests\Rating\StoreRatingRequest;
use App\Http\Requests\Rating\UpdateRatingRequest;
use App\Http\Resources\Rating\RatingResource;
use App\Models\Rating;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RatingController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    protected function resolveRateable(string $type, $id)
    {
        $class = 'App\\Models\\' . Str::studly(Str::singular($type));
        abort_unless(class_exists($class), 404, "Tipo inválido: {$type}");
        return $class::findOrFail($id);
    }

    public function index(string $type, int $rateableId): AnonymousResourceCollection
    {
        $rateable = $this->resolveRateable($type, $rateableId);
        $ratings = $rateable
            ->ratings()
            ->with('user')
            ->latest()
            ->paginate();
        return RatingResource::collection($ratings);
    }

    public function show(Rating $rating): RatingResource
    {
        return new RatingResource($rating);
    }

    public function store(StoreRatingRequest $request, string $type, int $rateableId): JsonResponse
    {
        $rateable = $this->resolveRateable($type, $rateableId);
        $rating = $rateable->ratings()->updateOrCreate(
            ['user_id' => auth()->id()],
            ['rating' => $request->validated('rating')]
        );
        $statusCode = $rating->wasRecentlyCreated ? 201 : 200;
        return (new RatingResource($rating))->response()->setStatusCode($statusCode);
    }


    public function update(UpdateRatingRequest $request, Rating $rating): RatingResource
    {
        $this->authorize('update', $rating);

        $rating->update($request->validated());

        return new RatingResource($rating);
    }

    public function destroy(Rating $rating): JsonResponse
    {
        $this->authorize('delete', $rating);
        $rating->delete();
        return response()->json(null, 204);
    }

    public function showUserRating(string $type, int $rateableId)
    {
        $rateable = $this->resolveRateable($type, $rateableId);
        $rating = $rateable->ratings()->where('user_id', auth()->id())->first();
        if (!$rating) {
            return response()->json(['message' => 'Nenhuma avaliação encontrada para este usuário.'], 404);
        }
        return new RatingResource($rating);
    }
}
