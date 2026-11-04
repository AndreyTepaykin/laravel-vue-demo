<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class ResetPasswordRequest extends FormRequest
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
            'token' => [
                'required',
                'string',
                'min:'.config('fields.min.password_reset_token'),
                'max:'.config('fields.max.password_reset_token'),
            ],
            'email' => [
                'required',
                'email',
                'min:'.config('fields.min.email'),
                'max:'.config('fields.max.email'),
            ],
            'password' => ['required', 'confirmed', Password::defaults()],
        ];
    }
}
