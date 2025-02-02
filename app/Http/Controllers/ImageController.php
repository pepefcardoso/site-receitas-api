<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Services\Image\CreateImage;
use App\Services\Image\DeleteImage;
use App\Services\Image\ListImage;
use App\Services\Image\ShowImage;
use App\Services\Image\UpdateImage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ImageController extends BaseController
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    public function index(ListImage $service)
    {
        $images = $service->list([]);
        return response()->json($images);
    }

    public function store(Request $request, CreateImage $service)
    {
        $this->authorize('create', Image::class);

        $data = $request->validate(Image::createRules());

        try {
            $model = $data['imageable_type']::findOrFail($data['imageable_id']);
            $image = $service->create($model, $data);

            return response()->json($image, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show(Image $image, ShowImage $service)
    {
        $image = $service->show($image->id);

        return response()->json($image);
    }

    public function update(Request $request, Image $image, UpdateImage $service)
    {
        $this->authorize('update', $image);

        $data = $request->validate([
            'file' => 'required|file|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        $updatedImage = $service->update($image->id, $data);

        return response()->json($updatedImage);
    }

    public function destroy(Image $image, DeleteImage $service)
    {
        $this->authorize('delete', $image);

        $response = $service->delete($image->id);

        return response()->json($response);
    }
}
