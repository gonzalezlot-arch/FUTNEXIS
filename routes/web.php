<?php

use App\Http\Middleware\EnsureLocalEnvironment;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/inicio');

Route::view('/interno/demo', 'dashboard.index', ['dashboard' => config('dashboard-demo')])
    ->middleware(EnsureLocalEnvironment::class)
    ->name('internal.demo');
