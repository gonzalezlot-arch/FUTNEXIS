<?php

namespace App\Modules\Core\Actions;

use App\Modules\Core\Models\EmailVerificationToken;
use Illuminate\Support\Facades\DB;

class ConfirmEmailVerification
{
    /**
     * Determine whether a token can still be used, without consuming it.
     */
    public function isUsable(#[\SensitiveParameter] string $token): bool
    {
        return EmailVerificationToken::query()
            ->where('token_hash', hash('sha256', $token))
            ->where('expires_at', '>', now())
            ->exists();
    }

    /**
     * Consume a token and set the password chosen by the owner of the address, atomically.
     * Returns false when the token is unknown, already used, replaced or expired.
     */
    public function handle(#[\SensitiveParameter] string $token, #[\SensitiveParameter] string $password): bool
    {
        return DB::transaction(function () use ($token, $password): bool {
            $verification = EmailVerificationToken::query()
                ->where('token_hash', hash('sha256', $token))
                ->lockForUpdate()
                ->first();

            if ($verification === null || $verification->expires_at->isPast()) {
                return false;
            }

            $verification->user->forceFill([
                'password' => $password,
                'email_verified_at' => now(),
            ])->save();

            $verification->delete();

            return true;
        });
    }
}
