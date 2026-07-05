<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'token', 'expires_at', 'used'])]
class ForgotPassword extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'used' => 'boolean',
        ];
    }

    protected $appends = ['url'];

    public function getUrlAttribute(): string
    {
        return config('app.frontend_url') . '/reset-password?token=' . $this->token;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
