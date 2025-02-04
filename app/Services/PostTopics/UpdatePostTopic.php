<?php

namespace App\Services\PostTopics;

use App\Models\PostTopic;
use App\Services\Image\UpdateImage;
use Illuminate\Support\Facades\DB;

class UpdatePostTopic
{
    protected UpdateImage $updateImageService;

    public function __construct(
        UpdateImage $updateImageService,
    ) {
        $this->updateImageService = $updateImageService;
    }

    public function update(PostTopic $PostTopic, array $data)
    {
        try {
            DB::beginTransaction();

            $PostTopic->fill($data);
            $PostTopic->save();

            $newImageFile = data_get($data, 'image');
            if ($newImageFile) {
                $currentImage = $PostTopic->image;
                $this->updateImageService->update($currentImage->id, $newImageFile);
            }

            DB::commit();

            return $PostTopic;
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
}
