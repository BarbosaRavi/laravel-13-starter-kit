<?php

namespace App\Observers;

use App\Models\User;
use App\Services\User\UserService;
use Illuminate\Support\Str;

class UserObserver
{
    public function creating(User $user): void
    {
        if (empty($user->email_confirmation_token)) {
            $user->email_confirmation_token = Str::random(64);
        }
    }

    public function created(User $user): void
    {
        if ($user->email_verified_at === null) {
            app(UserService::class)->sendMailConfirmation([
                'user' => $user,
            ]);
        }
    }
}