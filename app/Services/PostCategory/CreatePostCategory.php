<?php

namespace App\Services\PostCategory;

use App\Models\PostCategory;
use App\Services\Image\CreateImage;
use Illuminate\Support\Facades\DB;

class CreatePostCategory
{
    protected CreateImage $createImageService;

    public function __construct(
        CreateImage $createImageService,
    ) {
        $this->createImageService = $createImageService;
    }

    public function create(array $data)
    {
        try {
            DB::beginTransaction();

            $postCategory = PostCategory::create($data);

            $image = data_get($data, 'image');
            $this->createImageService->create($postCategory, $image);

            DB::commit();

            return $postCategory;
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
}
