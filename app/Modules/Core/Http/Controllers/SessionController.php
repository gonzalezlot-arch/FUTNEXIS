<?php

namespace App\Modules\Core\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Core\Actions\AuthenticateUser;
use App\Modules\Core\Actions\InvalidateUserSessions;
use App\Modules\Core\Http\Requests\LoginRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionController extends Controller
{
    public function create(): View
    {
        return view('core::auth.login');
    }

    public function store(LoginRequest $request, AuthenticateUser $authenticateUser): RedirectResponse
    {
        $user = $authenticateUser->handle(
            $request->validated('email'),
            $request->validated('password'),
            $request->ip(),
            $request->userAgent(),
        );

        Auth::login($user);
        $request->session()->regenerate();

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
     */
    public function destroyAll(Request $request, InvalidateUserSessions $invalidateUserSessions): RedirectResponse
    {
        $user = $request->user();

        Auth::logout();

        $invalidateUserSessions->handle($user);

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('status', 'Cerraste todas tus sesiones.');
    }
}
