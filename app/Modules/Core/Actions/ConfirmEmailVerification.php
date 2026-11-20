<?php

namespace App\Modules\Core\Actions;

use App\Models\User;
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
     * Returns false when the token is unknown, already used, replaced or expired, or when the
     * retention cleanup deleted the pending account first.
     *
     * The account row is locked before its token, the same order the retention cleanup follows
     * when it deletes the account and, by cascade, the token, so the two cannot deadlock.
     */
    public function handle(#[\SensitiveParameter] string $token, #[\SensitiveParameter] string $password): bool
    {
        $tokenHash = hash('sha256', $token);

        return DB::transaction(function () use ($tokenHash, $password): bool {
            $userId = EmailVerificationToken::query()->where('token_hash', $tokenHash)->value('user_id');

            if ($userId === null) {
                return false;
            }

            $user = User::query()->whereKey($userId)->lockForUpdate()->first();

            $verification = EmailVerificationToken::query()
                ->where('token_hash', $tokenHash)
                ->lockForUpdate()
                ->first();

            if ($user === null || $verification === null || (int) $verification->user_id !== (int) $user->id || $verification->expires_at->isPast()) {
                return false;
            }

            $user->forceFill([
                'password' => $password,
                'email_verified_at' => now(),
            ])->save();

            $verification->delete();

            return true;
        });
    }
}
