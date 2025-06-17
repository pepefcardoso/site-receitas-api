<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ToggleFavoriteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // A autorização para esta ação é simples e pode ser mantida no controller
        // ou movida para cá. Para consistência, podemos deixar no controller.
        // Se movida para cá, seria: return $this->user()->can('update', $this->user());
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'post_id' => 'sometimes|required|exists:posts,id',
            'recipe_id' => 'sometimes|required|exists:recipes,id',
        ];
    }
}