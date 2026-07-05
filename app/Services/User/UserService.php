<?php

namespace App\Services\User;

use App\Exceptions\ApiException;
use App\Jobs\SendEmailConfirmationMailJob;
use App\Jobs\SendForgotPasswordMailJob;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\ForgotPassword;
use Illuminate\Support\Str;

class UserService
{
    public function forgotPassword(array $data): void
    {
        $user = User::where('email', $data['email'])->first();

        if (!$user) {
            return;
        }

        $forgotPassword = ForgotPassword::create([
            'user_id' => $user->id,
            'token' => Str::uuid(),
            'expires_at' => now()->addHours(2),
        ]);
        SendForgotPasswordMailJob::dispatch($user, $forgotPassword);
    }

    public function resetPassword(array $data): void
    {
        $forgotPassword = ForgotPassword::query()
            ->where('token', $data['token'])
            ->where('expires_at', '>=', now())
            ->where('used', false)
            ->first();
        
        if (!$forgotPassword) {
            throw new ApiException('Token inválido ou expirado, solicite novamente a redefinição de senha');
        }

        $user = $forgotPassword->user;
        $user->update(['password' => Hash::make($data['password'])]);
        $forgotPassword->update(['used' => true]);
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

        SendEmailConfirmationMailJob::dispatch($user->id);
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