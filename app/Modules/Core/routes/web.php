<?php

use App\Modules\Core\Http\Controllers\EmailVerificationController;
use App\Modules\Core\Http\Controllers\PasswordResetController;
use App\Modules\Core\Http\Controllers\RegistrationController;
use App\Modules\Core\Http\Controllers\SessionController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::get('/registro', [RegistrationController::class, 'create'])->name('register');
    Route::post('/registro', [RegistrationController::class, 'store'])->middleware('throttle:core-register');

    Route::get('/ingresar', [SessionController::class, 'create'])->name('login');
    Route::post('/ingresar', [SessionController::class, 'store']);

    Route::get('/recuperar-contrasena', [PasswordResetController::class, 'request'])->name('password.request');
    Route::post('/recuperar-contrasena', [PasswordResetController::class, 'email'])
        ->middleware('throttle:core-password-email')
        ->name('password.email');

    Route::get('/restablecer-contrasena/{token}', [PasswordResetController::class, 'edit'])
        ->whereAlphaNumeric('token')
        ->middleware('cache.headers:no_store;private')
        ->name('password.reset');
    Route::post('/restablecer-contrasena', [PasswordResetController::class, 'update'])
        ->middleware('throttle:core-password-update')
        ->name('password.update');
});

Route::get('/verificar-correo', [EmailVerificationController::class, 'notice'])->name('verification.notice');
Route::post('/verificar-correo', [EmailVerificationController::class, 'resend'])
    ->middleware('throttle:core-verification-send')
    ->name('verification.send');

Route::get('/verificar-correo/{token}', [EmailVerificationController::class, 'show'])
    ->whereAlphaNumeric('token')
    ->middleware('cache.headers:no_store;private')
    ->name('verification.show');
Route::post('/verificar-correo/{token}', [EmailVerificationController::class, 'confirm'])
    ->whereAlphaNumeric('token')
    ->middleware('throttle:core-verification-confirm')
    ->name('verification.confirm');

Route::post('/salir', [SessionController::class, 'destroy'])->middleware('auth')->name('logout');
Route::post('/salir/todas', [SessionController::class, 'destroyAll'])->middleware('auth')->name('logout.all');

Route::middleware('core.authenticated')->group(function (): void {
    Route::view('/inicio', 'core::home')->name('home');
});
