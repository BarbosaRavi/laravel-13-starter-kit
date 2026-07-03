<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Override;

class AdminShowRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => ['required', 'uuid', 'exists:admins,id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'id' => 'ID do Admin',
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