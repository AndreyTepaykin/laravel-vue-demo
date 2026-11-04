<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
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
                'unique:users,email',
            ],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => [
                'required',
                'string',
                'min:'.config('fields.min.role'),
                'max:'.config('fields.max.role'),
                Rule::in(['admin', 'user']),
            ],
        ];
    }
}
