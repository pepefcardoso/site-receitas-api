<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ManagesResourceCaching;
use App\Models\Image;
use App\Services\Image\CreateImage;
use App\Services\Image\DeleteImage;
use App\Services\Image\UpdateImage;
use Illuminate\Http\Request;

class ImageController extends BaseController
{
    use ManagesResourceCaching;

    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    protected function getCacheTag(): string
    {
        return 'images';
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Image::class);

        $images = $this->getCachedAndPaginated($request, Image::query(), [], 'created_at');

        return response()->json($images);
    }

    public function store(Request $request, CreateImage $service)
    {
        return $this->execute(function () use ($request, $service) {
            $this->authorize('create', Image::class);

            $data = $request->validate(Image::createRules());
            $model = $data['imageable_type']::findOrFail($data['imageable_id']);
            $file = $data['file'];

            $imageData = $service->uploadOnly($file);
            $image = $service->createDbRecord($model, $imageData);

            $this->flushResourceCache();

            return response()->json($image, 201);
        });
    }

    public function show(Image $image)
    {
        return $this->execute(function () use ($image) {
            return response()->json($image);
        });
    }
    public function update(Request $request, Image $image, UpdateImage $updateService, CreateImage $createService)
    {
        return $this->execute(function () use ($request, $image, $updateService, $createService) {
            $this->authorize('update', $image);

            $data = $request->validate([
                'file' => 'required|file|mimes:jpeg,png,jpg,gif,svg,webp|max:2048'
            ]);
            $file = $data['file'];

            $newImageData = $createService->uploadOnly($file);
            $oldPath = $updateService->updateDbRecord($image, $newImageData);
            $updateService->deleteFile($oldPath);

            $this->flushResourceCache();

            return response()->json($image->fresh());
        });
    }

    public function destroy(Image $image, DeleteImage $service)
    {
        return $this->execute(function () use ($image, $service) {
            $this->authorize('delete', $image);

            $filePath = $service->deleteDbRecord($image);
            $service->deleteFile($filePath);

            $this->flushResourceCache();

            return response()->json(null, 204);
        });
    }
}
