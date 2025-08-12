<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

/**
 * @mixin \Illuminate\Http\Request
 */
class ToggleFavoriteRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }

        $targetUser = $this->route('user') ?? $user;

        if ($this->input('post_id') !== null) {
            return Gate::forUser($user)->allows('toggleFavoritePost', $targetUser);
        }

        if ($this->input('recipe_id') !== null) {
            return Gate::forUser($user)->allows('toggleFavoriteRecipe', $targetUser);
        }

        return false;
    }

    public function rules(): array
    {
        return [
            'post_id' => 'sometimes|required|exists:posts,id',
            'recipe_id' => 'sometimes|required|exists:recipes,id',
        ];
    }
}
