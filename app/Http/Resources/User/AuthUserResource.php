<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthUserResource extends JsonResource
{
    public static $token;

    public function toArray(Request $request): array
    {
        return [
            'user' => new UserResource($this),
            'token' => self::$token,
        ];
    }

    public function withToken(string $token)
    {
        self::$token = $token;
        return $this;
    }
}
