<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
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
                Rule::unique('users', 'email')->ignore($this->user()->id),
            ],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ];
    }
}
