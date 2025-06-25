<?php

namespace App\Http\Resources\User;

use App\Http\Resources\Recipe\ImageResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public static $wrap = 'data';

    public function toArray(Request $request): array
    {
        $auth = $request->user();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role?->name,
            'image' => $this->whenLoaded(
                'image',
                fn() => $this->image
                ? new ImageResource(
                    $this->image
                )
                : null
            ),
            'phone' => $this->when(
                $auth && $auth->can('view', $this->resource),
                $this->phone
            ),
            'birthday' => $this->when(
                $auth && $auth->can('view', $this->resource),
                $this->birthday
            ),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
