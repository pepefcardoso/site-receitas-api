<?php

namespace App\Services\Image;

use App\Models\Image;
use Exception;

class ListImage
{
    public function list(int $perPage = 10)
    {
        $query = Image::query();

        return $query->paginate($perPage);
    }
}
