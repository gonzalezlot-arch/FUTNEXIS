<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'dashboard.index', ['dashboard' => config('dashboard-demo')])
    ->name('dashboard');
