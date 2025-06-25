<?php

namespace App\Http\Controllers;

use App\Http\Requests\Rating\StoreRatingRequest;
use App\Http\Requests\Rating\UpdateRatingRequest;
use App\Models\Rating;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;

class RatingController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Cria ou atualiza uma avaliação para um item.
     * Esta abordagem garante que um usuário só possa ter uma avaliação por item.
     */
    public function store(StoreRatingRequest $request, Model $rateable): JsonResponse
    {
        $rating = $rateable->ratings()->updateOrCreate(
            ['user_id' => auth()->id()],
            ['rating' => $request->validated('rating')]
        );
        $statusCode = $rating->wasRecentlyCreated ? 201 : 200;
        return response()->json(['rating' => $rating->rating], $statusCode);
    }

    /**
     * Atualiza uma avaliação existente.
     * Este endpoint é mais explícito para um cliente que já sabe o ID da avaliação.
     */
    public function update(UpdateRatingRequest $request, Rating $rating): JsonResponse
    {
        $rating->update($request->validated());
        return response()->json(['rating' => $rating->rating]);
    }

    public function destroy(Rating $rating): JsonResponse
    {
        $this->authorize('delete', $rating);
        $rating->delete();
        return response()->json(null, 204);
    }
}
