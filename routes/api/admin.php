<?php 

use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth.api', 'can:admin.view'])->group(function () {
    Route::get('/', [AdminController::class, 'index']);
    Route::get('/{id}', [AdminController::class, 'show']);
});

Route::middleware(['auth.api', 'can:admin.create'])->group(function () {
    Route::post('/', [AdminController::class, 'store']);
});

Route::middleware(['auth.api', 'can:admin.update'])->group(function () {
    Route::put('/{id}', [AdminController::class, 'update']);   
});

Route::middleware(['auth.api', 'can:admin.restore'])->group(function () {
    Route::patch('/restore/{id}', [AdminController::class, 'restore']);
});

Route::middleware(['auth.api', 'can:admin.destroy'])->group(function () {
    Route::delete('/destroy/{id}', [AdminController::class, 'destroy']);
});

Route::middleware(['auth.api', 'can:admin.delete'])->group(function () {
    Route::delete('/{id}', [AdminController::class, 'delete']);
});