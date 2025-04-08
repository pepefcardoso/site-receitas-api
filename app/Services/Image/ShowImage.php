<?php

namespace App\Services\Image;

use App\Models\Image;

class ShowImage
{
    public function show(int $id)
    {
        return Image::findOrfail($id);
    }
}
