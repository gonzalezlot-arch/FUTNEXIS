<?php

namespace App\Modules\Core\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Core\Actions\ResetUserPassword;
use App\Modules\Core\Actions\SendPasswordResetLink;
use App\Modules\Core\Http\Requests\EmailOnlyRequest;
use App\Modules\Core\Http\Requests\ResetPasswordRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PasswordResetController extends Controller
{
    public function request(): View
    {
        return view('core::auth.forgot-password');
    }

    public function email(EmailOnlyRequest $request, SendPasswordResetLink $sendPasswordResetLink): RedirectResponse
    {
        $sendPasswordResetLink->handle($request->validated('email'));

        return back()->with('status', 'Si el correo está registrado, recibirás un enlace para restablecer tu contraseña en unos minutos.');
    }

    public function edit(Request $request, string $token): View
    {
        return view('core::auth.reset-password', ['token' => $token, 'email' => $request->query('email')]);
    }

    public function update(ResetPasswordRequest $request, ResetUserPassword $resetUserPassword): RedirectResponse
    {
        if (! $resetUserPassword->handle($request->safe()->only(['email', 'token', 'password']))) {
            return back()->withErrors(['email' => 'El enlace no es válido, ya se utilizó o venció. Solicita uno nuevo.']);
        }

        return redirect()->route('login')->with('status', 'Tu contraseña se actualizó. Inicia sesión con la nueva contraseña.');
    }
}
