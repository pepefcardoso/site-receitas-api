<?php

namespace App\Http\Requests\Rating;

use App\Models\Rating;
use Illuminate\Foundation\Http\FormRequest;

class StoreRatingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', arguments: Rating::class);
    }

    public function rules(): array
    {
        return ['rating' => 'required|integer|min:1|max:5'];
    }
}
