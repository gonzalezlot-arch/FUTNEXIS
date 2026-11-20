<?php

namespace App\Modules\Core\Actions;

use App\Models\User;
use App\Modules\Core\Models\EmailVerificationToken;

class ResendEmailVerification
{
    public const int COOLDOWN_SECONDS = 60;

    public function __construct(private IssueEmailVerification $issueEmailVerification) {}

    /**
     * Send a new link to a pending account. Unknown or verified addresses are ignored silently.
     */
    public function handle(string $email): void
    {
        $user = User::query()->where('email', $email)->whereNull('email_verified_at')->first();

        if ($user === null || $this->wasIssuedRecently($user)) {
            return;
        }

        $this->issueEmailVerification->handle($user);
    }

    private function wasIssuedRecently(User $user): bool
    {
        return EmailVerificationToken::query()
            ->where('user_id', $user->id)
            ->where('updated_at', '>', now()->subSeconds(self::COOLDOWN_SECONDS))
            ->exists();
    }
}
