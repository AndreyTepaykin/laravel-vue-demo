<?php

namespace App\Http\Requests\Api;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateManagedUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var User $user */
        $user = $this->route('user');

        return [
            'name' => [
                'required',
                'string',
                'min:'.config('fields.min.name'),
                'max:'.config('fields.max.name'),
            ],
            'email' => [
                'required',
                'string',
                'email',
                'min:'.config('fields.min.email'),
                'max:'.config('fields.max.email'),
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'role' => [
                'sometimes',
                'string',
                'min:'.config('fields.min.role'),
                'max:'.config('fields.max.role'),
                Rule::in(['admin', 'user']),
            ],
        ];
    }
}
