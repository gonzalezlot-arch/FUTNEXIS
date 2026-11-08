<?php

namespace App\Modules\Core\Actions;

use App\Models\User;
use App\Modules\Core\Support\MailDelivery;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Validation\ValidationException;

use function Illuminate\Support\defer;

class RegisterUser
{
    public const string DUPLICATE_EMAIL_MESSAGE = 'Este correo electrónico ya se encuentra registrado. Inicia sesión o recupera tu contraseña';

    public function __construct(
        private ResendEmailVerification $resendEmailVerification,
        private MailDelivery $mailDelivery,
    ) {}

    /**
     * Create a pending account without a password; only the owner of the address can set one (D5).
     * A pending address can be requested again, so a third party cannot reserve it.
     *
     * @throws ValidationException
     */
    public function handle(string $email): void
    {
        try {
            User::query()->firstOrCreate(['email' => $email]);
        } catch (UniqueConstraintViolationException) {
            // A simultaneous request created the account; it is handled below like any existing one.
        }

        if (User::query()->where('email', $email)->whereNotNull('email_verified_at')->exists()) {
            throw ValidationException::withMessages(['email' => self::DUPLICATE_EMAIL_MESSAGE]);
        }

        defer(fn () => $this->mailDelivery->attempt('registration', fn () => $this->resendEmailVerification->handle($email)));
    }
}
