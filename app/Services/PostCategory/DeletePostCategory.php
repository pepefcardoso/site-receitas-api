<?php

namespace App\Services\PostCategory;

use App\Models\PostCategory;
use Exception;
use Illuminate\Support\Facades\DB;

class DeletePostCategory
{
    public function delete(int $id)
    {
        try {
            DB::beginTransaction();

            $postCategory = PostCategory::findOrFail($id);

            if ($postCategory->posts()->exists()) {
                throw new Exception('This category cannot be deleted because it is associated with one or more posts.');
            }

            $postCategory->delete();
            DB::commit();

            return $postCategory;
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
