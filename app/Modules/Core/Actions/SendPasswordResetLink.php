<?php

namespace App\Modules\Core\Actions;

use App\Models\User;
use App\Modules\Core\Support\MailDelivery;
use Illuminate\Support\Facades\Password;

use function Illuminate\Support\defer;

class SendPasswordResetLink
{
    public function __construct(
        private ResendEmailVerification $resendEmailVerification,
        private MailDelivery $mailDelivery,
    ) {}

    /**
     * Run after the response, so its duration does not reveal whether the account exists.
     * A pending account has no password to recover: its owner receives the verification link instead.
     */
    public function handle(string $email): void
    {
        defer(fn () => $this->mailDelivery->attempt('password_reset', function () use ($email): void {
            $user = User::query()->where('email', $email)->first();

            if ($user === null) {
                return;
            }

            if (! $user->hasVerifiedEmail()) {
                $this->resendEmailVerification->handle($email);

                return;
            }

            Password::broker()->sendResetLink(['email' => $user->email]);
        }));
    }
}
