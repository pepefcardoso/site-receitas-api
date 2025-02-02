<?php

namespace App\Services\Image;

use App\Models\Image;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Exception;

class DeleteImage
{
    public function delete(int $id): Image|string
    {
        try {
            DB::beginTransaction();

            $image = Image::findOrFail($id);

            $image->delete();

            Storage::disk('s3')->delete(Image::$S3Directory . '/' . $image->name);

            DB::commit();

            return $image;
        } catch (Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }
    }
}
