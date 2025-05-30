<?php

namespace App\Services\PostCategory;

use App\Models\PostCategory;
use App\Services\Image\CreateImage;
use Illuminate\Support\Facades\DB;

class CreatePostCategory
{
    public function create(array $data)
    {
        try {
            DB::beginTransaction();

            $postCategory = PostCategory::create($data);

            DB::commit();

            return $postCategory;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
