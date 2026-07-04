<?php

namespace App\Http\Requests\Admin;

use App\Rules\Admin\ActiveAdmin;
use Illuminate\Foundation\Http\FormRequest;
use Override;

class AdminDeleteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => ['required', 'uuid', new ActiveAdmin()],
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