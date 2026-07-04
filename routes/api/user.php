<?php

use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/forgot-password', [UserController::class, 'forgotPassword']);
Route::post('/reset-password', [UserController::class, 'resetPassword']);
Route::get('/confirm-mail', [UserController::class, 'confirmMail']);
Route::post('/resend-mail-confirmation', [UserController::class, 'resendMailConfirmation']);

Route::middleware(['auth.api'])->group(function () {
    Route::put('/password', [UserController::class, 'updatePassword']);
});