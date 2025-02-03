<?php

namespace App\Services\Image;

use App\Models\Image;
use Auth;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CreateImage
{
    public function create(Model $model, array $data): Image|string
    {
        try {
            DB::beginTransaction();

            $file = data_get($data, 'file');
            throw_if(!$file, new Exception('File not found'));

            $name = uniqid() . '.' . $file->extension();

            $path = Storage::disk('s3')->putFileAs(Image::$S3Directory, $file, $name);

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
