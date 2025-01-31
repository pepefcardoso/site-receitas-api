<?php

namespace App\Services\PostCategory;

use App\Models\PostCategory;
use Illuminate\Support\Facades\DB;

class DeletePostCategory
{
    public function delete(PostCategory $postCategory)
    {
        try {
            DB::beginTransaction();

            $postCategory->delete();

            DB::commit();

            return $postCategory;
        } catch (\Exception $e) {
            DB::rollback();
            return $e->getMessage();
        }
    }
}
