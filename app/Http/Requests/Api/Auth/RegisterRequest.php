<?php

namespace App\Http\Requests\Api\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
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
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'password.confirmed' => 'The password confirmation does not match.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('confirm_password') && ! $this->has('password_confirmation')) {
            $this->merge([
                'password_confirmation' => $this->input('confirm_password'),
            ]);
        }
    }
}
