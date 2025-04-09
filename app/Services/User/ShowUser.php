<?php

namespace App\Services\User;

use App\Models\User;

class ShowUser
{
    public function show($id)
    {
        return User::select('id', 'name')
        ->with([
            'image' => fn ($query) => $query->select('id', 'path', 'imageable_id', 'imageable_type')
        ])
            ->findOrFail($id);
    }
}
