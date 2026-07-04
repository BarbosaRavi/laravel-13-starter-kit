<?php

namespace App\Services\User;

use App\Exceptions\ApiException;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use App\Mails\MailConfirmationMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserService
{
    public function forgotPassword(array $data): void
    {
        $status = Password::sendResetLink([
            'email' => $data['email'],
        ]);

        if ($status !== Password::ResetLinkSent) {
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

    public function confirmMail(array $data): void
    {
        $user = User::query()
            ->where('email_confirmation_token', $data['token'])
            ->firstOrFail();

        $user->forceFill([
            'email_confirmation_token' => null,
            'email_verified_at' => now(),
            'remember_token' => Str::random(60),
        ])->save();
    }

    public function sendMailConfirmation(array $data): void
    {
        $user = $data['user'];

        if ($user->email_verified_at !== null) {
            return;
        }

        if (empty($user->email_confirmation_token)) {
            $user->forceFill([
                'email_confirmation_token' => Str::random(64),
            ])->save();
        }

        $confirmationUrl = config('app.url')
            . '/api/user/confirm-mail?token='
            . urlencode($user->email_confirmation_token);

        Mail::to($user->email, $user->name)->queue(
            new MailConfirmationMail($user, $confirmationUrl)
        );
    }

    public function resendMailConfirmation(array $data): void
    {
        $user = User::query()
            ->where('email', $data['email'])
            ->first();

        if (! $user || $user->email_verified_at !== null) {
            return;
        }

        $user->forceFill([
            'email_confirmation_token' => Str::random(64),
        ])->save();

        $this->sendMailConfirmation([
            'user' => $user,
        ]);
    }

    public function changePassword(array $data): void
    {
        $user = Auth::user();
        
        if (! Hash::check($data['current_password'], $user->password)) {
            throw new ApiException('Senha atual inválida');
        }

        $user->forceFill([
            'password' => Hash::make($data['password']),
            'remember_token' => Str::random(60),
        ])->save();
    }
}