<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthUserResource extends JsonResource
{
    public static string $token;
    public function toArray(Request $request): array
    {
        return [
            'token' => self::$token,
            'user' => new UserResource($this->resource),
        ];
    }
    public function withToken(string $token): self
    {
        self::$token = $token;
        return $this;
    }
}
