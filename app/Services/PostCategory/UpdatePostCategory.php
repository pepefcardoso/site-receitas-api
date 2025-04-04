<?php

namespace App\Services\PostCategory;

use App\Models\PostCategory;
use Illuminate\Support\Facades\DB;

class UpdatePostCategory
{
    public function update(int $postCategoryId, array $data)
    {
        try {
            DB::beginTransaction();

            $postCategory = PostCategory::findOrFail($postCategoryId);
            $postCategory->fill($data);
            $postCategory->save();

            DB::commit();

            return $postCategory;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

}
