<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Services\Rating\CreateRating;
use App\Services\Rating\DeleteRating;
use App\Services\Rating\ListRatings;
use App\Services\Rating\ShowRating;
use App\Services\Rating\UpdateRating;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class RatingController extends BaseController
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    public function index(Request $request, ListRatings $service)
    {
        return $this->execute(function () use ($request, $service) {
            $perPage = $request->input('per_page', 10);
            $filters = [
                'rateable_id' => $request->input('rateable_id'),
                'rateable_type' => $request->input('rateable_type'),
            ];
            $ratings = $service->list($filters, $perPage);
            return response()->json($ratings);
        });
    }

    public function store(Request $request, CreateRating $service)
    {
        return $this->execute(function () use ($request, $service) {
            $this->authorize('create', Rating::class);

            $data = $request->validate(Rating::rules());

            $modelClass = 'App\\Models\\' . $data['rateable_type'];
            $model = $modelClass::findOrFail($data['rateable_id']);
            $rating = $data['rating'];

            $rating = $service->create($model, $rating);
            return response()->json($rating, 201);
        });
    }

    public function show(Rating $rating, ShowRating $service)
    {
        return $this->execute(function () use ($rating, $service) {
            $rating = $service->show($rating->id);
            return response()->json($rating);
        });
    }

    public function update(Request $request, Rating $rating, UpdateRating $service)
    {
        return $this->execute(function () use ($request, $rating, $service) {
            $this->authorize('update', $rating);

            $data = $request->validate([
                'rating' => 'required|integer|min:0|max:5',
            ]);
            $newRating = $data['rating'];

            $updatedRating = $service->update($rating->id, $newRating);
            return response()->json($updatedRating);
        });
    }

    public function destroy(Rating $rating, DeleteRating $service)
    {
        return $this->execute(function () use ($rating, $service) {
            $this->authorize('delete', $rating);
            $response = $service->delete($rating->id);
            return response()->json($response);
        });
    }
}
