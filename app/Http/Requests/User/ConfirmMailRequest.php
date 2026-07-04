<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class ConfirmMailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'token' => ['required', 'string', 'exists:users,email_confirmation_token'],
        ];
    }

    public function attributes(): array
    {
        return [
            'token' => 'Token',
        ];
    }
}