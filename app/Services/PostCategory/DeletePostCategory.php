<?php

namespace App\Services\PostCategory;

use App\Models\PostCategory;
use Illuminate\Support\Facades\DB;

class DeletePostCategory
{
    public function delete(int $id)
    {
        \Log::info('DeletePostCategory service called', ['id' => $id]);

        try {
            DB::beginTransaction();

            $postCategory = PostCategory::findOrFail($id);

            if ($postCategory->posts()->exists()) {
                \Log::info('Category has related posts, cannot delete');
                throw new \Exception('This category cannot be deleted because it is associated with one or more posts.');
            }

            \Log::info('Category has no related posts, proceeding with deletion');

            $postCategory->delete();

            DB::commit();

            return $postCategory;
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error deleting category', ['error' => $e->getMessage()]);
            return $e->getMessage();
        }
    }

}
