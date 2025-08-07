<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Services\Image\CreateImage;
use App\Services\Image\DeleteImage;
use App\Services\Image\UpdateImage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class ImageController extends BaseController
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Image::class);

        $perPage = $request->input('per_page', 10);
        $orderBy = $request->input('order_by', 'created_at');
        $orderDirection = $request->input('order_direction', 'desc');

        $images = Image::query()
            ->orderBy($orderBy, $orderDirection)
            ->paginate($perPage);

        return response()->json($images);
    }

    public function store(Request $request, CreateImage $service)
    {
        $this->authorize('view', $image);
        return $this->execute(function () use ($request, $service) {
            $this->authorize('create', Image::class);

            $data = $request->validate(Image::createRules());

            $model = $data['imageable_type']::findOrFail($data['imageable_id']);
            $file = $data['file'];

            $image = $service->create($model, $file);
            return response()->json($image, 201);
        });
    }

    public function show(Image $image)
    {
        return $this->execute(function () use ($image) {
            return response()->json($image);
        });
    }

    public function update(Request $request, Image $image, UpdateImage $service)
    {
        return $this->execute(function () use ($request, $image, $service) {
            $this->authorize('update', $image);

            $data = $request->validate([
                'file' => 'required|file|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);
            $file = $data['file'];

            $updatedImage = $service->update($image, $file);
            return response()->json($updatedImage);
        });
    }

    public function destroy(Image $image, DeleteImage $service)
    {
        return $this->execute(function () use ($image, $service) {
            $this->authorize('delete', $image);
            $response = $service->delete($image);
            return response()->json($response);
        });
    }
}
