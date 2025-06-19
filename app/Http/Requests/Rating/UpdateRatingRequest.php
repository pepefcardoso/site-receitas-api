<?php

namespace App\Http\Requests\Rating;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRatingRequest extends FormRequest
{
    public function authorize(): bool
    {
        $rating = $this->route('rating');
        return $this->user()->can('update', $rating);
    }

    public function rules(): array
    {
        return ['rating' => 'required|integer|min:1|max:5'];
    }
}
