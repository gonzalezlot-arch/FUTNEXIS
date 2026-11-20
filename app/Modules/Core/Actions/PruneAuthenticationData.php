<?php

namespace App\Modules\Core\Actions;

use App\Models\User;
use App\Modules\Core\Models\EmailVerificationToken;
use App\Modules\Core\Models\LoginAttempt;
use App\Modules\Core\Models\LoginThrottle;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Lottery;

use function Illuminate\Support\defer;

/**
 * Applies the approved retention periods (DEC-CORE-015, detailed in FN-OPS-CORE-001).
 */
class PruneAuthenticationData
{
    public const int LOGIN_ATTEMPT_RETENTION_DAYS = 90;

    public const int IDLE_COUNTER_RETENTION_DAYS = 30;

    public const int EXPIRED_LOCK_RETENTION_HOURS = 24;

    public const int UNVERIFIED_ACCOUNT_RETENTION_DAYS = 7;

    private const int BATCH_SIZE = 1000;

    /**
     * Cleans up without a cron job as well, for hosting where scheduled tasks are unavailable.
     */
    public function runOccasionally(): void
    {
        Lottery::odds(1, 100)
            ->winner(fn () => defer(fn () => rescue(fn () => $this->handle())))
            ->choose();
    }

    /**
     * Delete expired data in bounded batches and return the rows removed per kind.
     *
     * @return array<string, int>
     */
    public function handle(): array
    {
        return [
            'login_attempts' => LoginAttempt::query()
                ->where('attempted_at', '<', now()->subDays(self::LOGIN_ATTEMPT_RETENTION_DAYS))
                ->limit(self::BATCH_SIZE)
                ->delete(),
            'login_throttles' => LoginThrottle::query()
                ->where(fn ($query) => $query
                    ->where('locked_until', '<', now()->subHours(self::EXPIRED_LOCK_RETENTION_HOURS))
                    ->orWhere(fn ($query) => $query
                        ->whereNull('locked_until')
                        ->where('updated_at', '<', now()->subDays(self::IDLE_COUNTER_RETENTION_DAYS))))
                ->limit(self::BATCH_SIZE)
                ->delete(),
            'email_verification_tokens' => EmailVerificationToken::query()
                ->where('expires_at', '<', now())
                ->limit(self::BATCH_SIZE)
                ->delete(),
            'password_reset_tokens' => DB::table((string) config('auth.passwords.users.table'))
                ->where('created_at', '<', now()->subMinutes((int) config('auth.passwords.users.expire')))
                ->limit(self::BATCH_SIZE)
                ->delete(),
            'unverified_users' => User::query()
                ->whereNull('email_verified_at')
                ->where('created_at', '<', now()->subDays(self::UNVERIFIED_ACCOUNT_RETENTION_DAYS))
                ->limit(self::BATCH_SIZE)
                ->delete(),
        ];
    }
}
