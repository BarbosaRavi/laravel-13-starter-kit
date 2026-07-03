<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Override;

class AdminUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => ['required', 'uuid', 'exists:admins,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
        ];
    }

    public function attributes(): array
    {
        return [
            'id' => 'ID do Admin',
            'name' => 'Nome',
            'email' => 'Email',
        ];
    }

    #[Override]
    public function prepareForValidation()
    {
        $this->merge([
            'id' => $this->route('id'),
        ]);
    }
}