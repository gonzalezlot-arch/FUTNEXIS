<?php

namespace App\Modules\Core\Actions;

use App\Models\User;
use App\Modules\Core\Models\EmailVerificationToken;
use App\Modules\Core\Notifications\VerifyEmailNotification;
use Illuminate\Support\Str;

class IssueEmailVerification
{
    public const int EXPIRATION_HOURS = 24;

    /**
     * Replace any previous token and email the new link. Only the hash of the token is stored.
     */
    public function handle(User $user): void
    {
        $token = Str::random(64);

        EmailVerificationToken::query()->upsert(
            [[
                'user_id' => $user->id,
                'token_hash' => hash('sha256', $token),
                'expires_at' => now()->addHours(self::EXPIRATION_HOURS),
            ]],
            ['user_id'],
            ['token_hash', 'expires_at'],
        );

        $user->notify(new VerifyEmailNotification($token));
    }
}
