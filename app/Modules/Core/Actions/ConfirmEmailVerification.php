<?php

namespace App\Modules\Core\Actions;

use App\Modules\Core\Models\EmailVerificationToken;
use Illuminate\Support\Facades\DB;

class ConfirmEmailVerification
{
    /**
     * Consume a token. Returns false when it is unknown, already used, replaced or expired.
     */
    public function handle(#[\SensitiveParameter] string $token): bool
    {
        return DB::transaction(function () use ($token): bool {
            $verification = EmailVerificationToken::query()
                ->where('token_hash', hash('sha256', $token))
                ->lockForUpdate()
                ->first();

            if ($verification === null || $verification->expires_at->isPast()) {
                return false;
            }

            $verification->user->markEmailAsVerified();
            $verification->delete();

            return true;
        });
    }
}
