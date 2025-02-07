<?php

namespace App\Services\PostCategory;

use App\Models\PostCategory;
use App\Services\Image\UpdateImage;
use Illuminate\Support\Facades\DB;

class UpdatePostCategory
{
    public function update(PostCategory $postCategory, array $data)
    {
        try {
            DB::beginTransaction();

            $postCategory->fill($data);
            $postCategory->save();

            DB::commit();

            return $postCategory;
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }

}
