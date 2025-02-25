<?php

namespace App\Services\Image;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Models\Image;

class GetTemporaryUrl
{
    public function temporaryUrl(Image $image): string
    {
        return Storage::disk('s3')->temporaryUrl(
            $image->path, Carbon::now()->addMinutes(5)
        );
    }
}
