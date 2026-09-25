<?php

use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

Route::post('/forgot-password', [UserController::class, 'forgotPassword'])->middleware('throttle:emails');
Route::post('/resend-mail-confirmation', [UserController::class, 'resendMailConfirmation'])->middleware('throttle:emails');
Route::post('/reset-password', [UserController::class, 'resetPassword'])->middleware('throttle:tokens');
Route::get('/confirm-mail', [UserController::class, 'confirmMail'])->middleware('throttle:tokens');

Route::middleware(['auth.api'])->group(function () {
    Route::put('/password', [UserController::class, 'updatePassword']);
});