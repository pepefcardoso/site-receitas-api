<?php

namespace App\Services\Image;

use App\Models\Image;
use Exception;

class ListImage
{
    public function list(array $filters)
    {
        return Image::all();
    }
}
