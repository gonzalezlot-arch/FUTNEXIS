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

        return redirect()->route('password.request')
            ->with('status', 'Recibimos tu solicitud. Si el correo está registrado, te llegará un enlace para restablecer tu contraseña.');
    }

    public function edit(Request $request, string $token): View
    {
        $email = $request->query('email');

        return view('core::auth.reset-password', [
            'token' => $token,
            'email' => is_string($email) && mb_check_encoding($email, 'UTF-8') ? $email : null,
        ]);
    }

    public function update(ResetPasswordRequest $request, ResetUserPassword $resetUserPassword): RedirectResponse
    {
        $credentials = $request->safe()->only(['email', 'token', 'password']);

        if (! $resetUserPassword->handle($credentials, $request->ip(), $request->userAgent())) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'El enlace no es válido, ya se utilizó o venció. Solicita uno nuevo.']);
        }

        return redirect()->route('login')->with('status', 'Tu contraseña se actualizó. Inicia sesión con la nueva contraseña.');
    }
}
