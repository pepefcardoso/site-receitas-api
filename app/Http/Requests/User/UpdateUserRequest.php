<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        $userToUpdate = $this->route('user');
        return $this->user()->can('update', $userToUpdate);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $userId = $this->route('user')->id;

        return [
            'name' => 'sometimes|required|string|min:3|max:100',
            'email' => [
                'sometimes',
                'required',
                'email',
                Rule::unique('users')->ignore($userId),
            ],
            'phone' => [
                'sometimes',
                'required',
                'string',
                'regex:/^\d{10,11}$/',
                Rule::unique('users')->ignore($userId),
            ],
            'birthday' => 'nullable|date|before_or_equal:today',
            'password' => 'nullable|string|min:8|max:99|confirmed',
        ];
    }
}
