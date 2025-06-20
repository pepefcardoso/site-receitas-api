<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthUserResource extends JsonResource
{
    public static $wrap = null;

    private string $token;

    public function withToken(string $token): self
    {
        $this->token = $token;
        return $this->additional(['token' => $token]);
    }

    public function toArray(Request $request): array
    {
        return [
            'user' => new UserResource($this->resource->load('image')),
        ];
    }

    public function withResponse($request, $response): void
    {
        $response->setStatusCode(201);
    }
}
