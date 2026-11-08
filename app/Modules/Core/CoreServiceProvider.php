<?php

namespace App\Modules\Core;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class CoreServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the CORE module.
     */
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/resources/views', 'core');

        Password::defaults(fn (): Password => Password::min(8)->mixedCase()->numbers()->symbols());

        RateLimiter::for('core-forms', fn (Request $request): Limit => Limit::perMinute(10)
            ->by($request->ip())
            ->response(fn (): RedirectResponse => back()->withErrors([
                'email' => 'Demasiadas solicitudes seguidas. Espera un momento e inténtalo de nuevo.',
            ])));

        if (! $this->app->routesAreCached()) {
            Route::middleware('web')->group(__DIR__.'/routes/web.php');
        }
    }
}
