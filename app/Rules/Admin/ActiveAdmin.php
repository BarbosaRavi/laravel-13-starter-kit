<?php

namespace App\Rules\Admin;

use App\Models\Admin;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ActiveAdmin implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $exists = Admin::query()
            ->whereKey($value)
            ->whereHas('user')
            ->exists();

        if (! $exists) {
            $fail('O admin informado não existe ou está excluído.');
        }
    }
}