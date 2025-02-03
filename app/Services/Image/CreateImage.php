<?php

namespace App\Services\Image;

use App\Models\Image;
use Auth;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Log;

class CreateImage
{
    public function create(Model $model, UploadedFile $file): Image|string
    {
        try {
            DB::beginTransaction();

            $name = uniqid() . '.' . $file->extension();

            $path = Storage::disk('s3')->putFileAs(Image::$S3Directory, $file, $name);
            Log::info($path);

            $userId = Auth::id();

            $image = $model->image()->create([
                'path' => $path,
                'name' => $name,
                'user_id' => $userId,
            ]);

            DB::commit();

            return $image;
        } catch (Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }
    }
}
