<?php

namespace App\Services\Image;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use App\Models\Image;

class GetTemporaryUrl
{
    public function temporaryUrl(Image $image): string
    {
        $cacheKey = 'image_url_' . $image->id;
        $cacheTtl = 5;

        $url = Cache::get($cacheKey);

        if (!$url) {
            $url = Storage::disk('s3')->temporaryUrl(
                $image->path,
                Carbon::now()->addMinutes($cacheTtl)
            );
            Cache::put($cacheKey, $url, now()->addMinutes($cacheTtl));
        }

        return $url;
    }
}
