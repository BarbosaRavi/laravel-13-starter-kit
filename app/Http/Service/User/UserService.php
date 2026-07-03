<?php

namespace App\Services\User;

use App\Exceptions\ApiException;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class UserService
{
    public function forgotPassword(array $data): void
    {
        $status = Password::sendResetLink([
            'email' => $data['email'],
        ]);

        if ($status !== Password::RESET_LINK_SENT) {
            throw new ApiException(__($status));
        }
    }

    public function resetPassword(array $data): void
    {
        $status = Password::reset(
            [
                'email' => $data['email'],
                'password' => $data['password'],
                'password_confirmation' => $data['password_confirmation'],
                'token' => $data['token'],
            ],
            function (User $user, string $password): void {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user)); 
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            throw new ApiException(__($status));
        }
    }

    public function setupPassword(array $data): void
    {
        $user = User::query()
            ->where('email_confirmation_token', $data['token'])
            ->firstOrFail();

        $user->forceFill([
            'password' => Hash::make($data['password']),
            'email_confirmation_token' => null,
            'email_verified_at' => now(),
            'remember_token' => Str::random(60),
        ])->save();
    }

    public function changePassword(User $user, array $data): void
    {
        if (! Hash::check($data['current_password'], $user->password)) {
            throw new ApiException('Senha atual inválida');
        }

        $user->forceFill([
            'password' => Hash::make($data['password']),
            'remember_token' => Str::random(60),
        ])->save();
    }
}