<?php

namespace App\Http\Requests\User;

use App\Enum\RolesEnum;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Apenas usuários autorizados a ver qualquer usuário (internos)
        // podem alterar uma role.
        return $this->user()->can('viewAny', User::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $roleValues = array_map(fn($case) => $case->value, RolesEnum::cases());

        return [
            'user_id' => 'required|exists:users,id',
            'role' => ['required', Rule::in($roleValues)],
        ];
    }
}
