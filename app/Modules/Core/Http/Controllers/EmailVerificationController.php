<?php

namespace App\Modules\Core\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Core\Actions\ConfirmEmailVerification;
use App\Modules\Core\Actions\ResendEmailVerification;
use App\Modules\Core\Http\Requests\EmailOnlyRequest;
use App\Modules\Core\Http\Requests\SetInitialPasswordRequest;
use App\Modules\Core\Support\MailDelivery;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

use function Illuminate\Support\defer;

class EmailVerificationController extends Controller
{
    private const string INVALID_LINK_MESSAGE = 'El enlace no es válido, ya se utilizó o venció. Solicita uno nuevo.';

    public function notice(): View
    {
        return view('core::auth.verify-notice');
    }

    public function resend(EmailOnlyRequest $request, ResendEmailVerification $resendEmailVerification, MailDelivery $mailDelivery): RedirectResponse
    {
        $email = $request->validated('email');

        defer(fn () => $mailDelivery->attempt('verification_resend', fn () => $resendEmailVerification->handle($email)));

        return redirect()->route('verification.notice')
            ->with('status', 'Recibimos tu solicitud. Si tu cuenta está pendiente de activación, te llegará un nuevo enlace.');
    }

    /**
     * Show the password form without consuming the token, because mail scanners open links automatically.
     */
    public function show(string $token, ConfirmEmailVerification $confirmEmailVerification): View|RedirectResponse
    {
        if (! $confirmEmailVerification->isUsable($token)) {
            return redirect()->route('verification.notice')->withErrors(['email' => self::INVALID_LINK_MESSAGE]);
        }

        return view('core::auth.verify-confirm', ['token' => $token]);
    }

    public function confirm(string $token, SetInitialPasswordRequest $request, ConfirmEmailVerification $confirmEmailVerification): RedirectResponse
    {
        if (! $confirmEmailVerification->handle($token, $request->validated('password'))) {
            return redirect()->route('verification.notice')->withErrors(['email' => self::INVALID_LINK_MESSAGE]);
        }

        return redirect()->route('login')->with('status', 'Tu cuenta está activa. Ya puedes iniciar sesión.');
    }
}
