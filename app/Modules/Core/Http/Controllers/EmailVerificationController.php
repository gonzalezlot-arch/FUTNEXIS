<?php

namespace App\Modules\Core\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Core\Actions\ConfirmEmailVerification;
use App\Modules\Core\Actions\ResendEmailVerification;
use App\Modules\Core\Http\Requests\EmailOnlyRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

use function Illuminate\Support\defer;

class EmailVerificationController extends Controller
{
    public function notice(): View
    {
        return view('core::auth.verify-notice');
    }

    public function resend(EmailOnlyRequest $request, ResendEmailVerification $resendEmailVerification): RedirectResponse
    {
        $email = $request->validated('email');

        defer(fn () => rescue(fn () => $resendEmailVerification->handle($email)));

        return back()->with('status', 'Si tu cuenta está pendiente de verificación, recibirás un nuevo enlace en unos minutos.');
    }

    /**
     * Show a confirmation page instead of consuming the token, because mail scanners open links automatically.
     */
    public function show(string $token): View
    {
        return view('core::auth.verify-confirm', ['token' => $token]);
    }

    public function confirm(string $token, ConfirmEmailVerification $confirmEmailVerification): RedirectResponse
    {
        if (! $confirmEmailVerification->handle($token)) {
            return redirect()->route('verification.notice')
                ->withErrors(['email' => 'El enlace no es válido, ya se utilizó o venció. Solicita uno nuevo.']);
        }

        return redirect()->route('login')->with('status', 'Tu correo quedó verificado. Ya puedes iniciar sesión.');
    }
}
