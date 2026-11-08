<?php

namespace App\Modules\Core;

use App\Modules\Core\Actions\PruneAuthenticationData;
use App\Modules\Core\Console\PruneAuthenticationDataCommand;
use App\Modules\Core\Http\Middleware\EnsureSessionIsCurrent;
use App\Modules\Core\Http\Middleware\StartSessionWithoutTokenUrls;
use App\Modules\Core\Rules\ContainsSpecialCharacter;
use App\Modules\Core\Rules\FitsBcryptLimit;
use App\Modules\Core\Support\IpAddress;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Cache\Factory as CacheFactory;
use Illuminate\Http\Middleware\TrustProxies;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Session\SessionManager;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class CoreServiceProvider extends ServiceProvider
{
    /**
     * Each form has its own limit, so one flow cannot exhaust another.
     *
     * @var array<int, string>
     */
    private const array FORM_LIMITERS = [
        'core-register',
        'core-password-email',
        'core-password-update',
        'core-verification-send',
        'core-verification-confirm',
    ];

    private const string FORM_THROTTLED_MESSAGE = 'Demasiadas solicitudes seguidas. Espera un momento e inténtalo de nuevo.';

    /**
     * Register the CORE module services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/config/core.php', 'core');

        $this->app->singleton(StartSession::class, fn ($app): StartSession => new StartSessionWithoutTokenUrls(
            $app->make(SessionManager::class),
            fn () => $app->make(CacheFactory::class),
        ));
    }

    /**
     * Bootstrap the CORE module.
     */
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/resources/views', 'core');

        Password::defaults(fn (): Password => Password::min(8)
            ->mixedCase()
            ->numbers()
            ->rules([new ContainsSpecialCharacter, new FitsBcryptLimit]));

        $this->configureTrustedProxies();
        $this->configureFormLimiters();

        Route::middlewareGroup('core.authenticated', ['auth', 'auth.session', EnsureSessionIsCurrent::class, 'verified']);

        if (! $this->app->routesAreCached()) {
            Route::middleware('web')->group(__DIR__.'/routes/web.php');
        }

        if ($this->app->runningInConsole()) {
            $this->commands([PruneAuthenticationDataCommand::class]);
        }

        $this->callAfterResolving(Schedule::class, function (Schedule $schedule): void {
            $schedule->call(fn () => $this->app->make(PruneAuthenticationData::class)->handle())
                ->name('core:prune-auth-data')
                ->daily();
        });
    }

    /**
     * Parse the configured proxies. The wildcards are discarded because they would trust any client.
     *
     * @return array<int, string>
     */
    public static function parseTrustedProxies(string $configured): array
    {
        return array_values(array_filter(
            array_map('trim', explode(',', $configured)),
            fn (string $proxy): bool => $proxy !== '' && $proxy !== '*' && $proxy !== '**',
        ));
    }

    /**
     * Forwarded headers are honoured only for connections coming from the configured proxies.
     */
    private function configureTrustedProxies(): void
    {
        $proxies = self::parseTrustedProxies((string) config('core.trusted_proxies'));

        if ($proxies !== []) {
            TrustProxies::at($proxies);
        }
    }

    private function configureFormLimiters(): void
    {
        foreach (self::FORM_LIMITERS as $limiter) {
            RateLimiter::for($limiter, fn (Request $request): Limit => Limit::perMinute(10)
                ->by(IpAddress::bucket($request->ip()))
                ->response(fn (Request $request): RedirectResponse => $this->formThrottledResponse($request)));
        }
    }

    /**
     * Pages whose address carries a token are not kept as the previous URL, so they are named explicitly.
     */
    private function formThrottledResponse(Request $request): RedirectResponse
    {
        $response = match (true) {
            $request->routeIs('verification.confirm') => redirect()->route('verification.show', ['token' => $request->route('token')]),
            $request->routeIs('password.update') => redirect()->route('password.request'),
            default => back(),
        };

        return $response->withErrors(['form' => self::FORM_THROTTLED_MESSAGE]);
    }
}
