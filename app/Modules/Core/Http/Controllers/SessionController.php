<?php

namespace App\Modules\Core\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Core\Actions\AuthenticateUser;
use App\Modules\Core\Actions\InvalidateUserSessions;
use App\Modules\Core\Actions\PruneAuthenticationData;
use App\Modules\Core\Http\Middleware\EnsureSessionIsCurrent;
use App\Modules\Core\Http\Requests\LoginRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

class SessionController extends Controller
{
    public function create(): View
    {
        return view('core::auth.login');
    }

    public function store(LoginRequest $request, AuthenticateUser $authenticateUser, PruneAuthenticationData $pruneAuthenticationData): RedirectResponse
    {
        $pruneAuthenticationData->runOccasionally();

        $user = $authenticateUser->handle(
            $request->validated('email'),
            $request->validated('password'),
            $request->ip(),
            $request->userAgent(),
        );

        Auth::login($user);
        $request->session()->regenerate();
        $request->session()->put(EnsureSessionIsCurrent::SESSION_KEY, $user->session_epoch);

        return redirect()->intended(route('home'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Cerraste tu sesión.');
    }

    /**
     * Close every session of the account, including the current one (RN-CORE-009).
     * Success is only reported once the invalidation has been applied.
     */
    public function destroyAll(Request $request, InvalidateUserSessions $invalidateUserSessions): RedirectResponse
    {
        try {
            $invalidateUserSessions->handle($request->user());
        } catch (Throwable $exception) {
            report($exception);

            return back()->withErrors(['form' => 'No pudimos cerrar todas tus sesiones. Inténtalo de nuevo.']);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Cerraste todas tus sesiones.');
    }
}
