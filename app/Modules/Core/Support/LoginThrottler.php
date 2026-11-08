<?php

namespace App\Modules\Core\Support;

use App\Modules\Core\Models\LoginThrottle;
use Illuminate\Support\Facades\DB;

/**
 * Applies the login attempt limits per account and per IP address.
 *
 * Pending difference: FN-ARC-CORE-001 section 5 and the CORE 01 request call for additional controls by IP and
 * device, while DEC-CORE-005 keeps device information as audit data and establishes no lock by device. This class
 * therefore locks by account and IP only; the device is recorded in login_attempts. Adding a device lock requires
 * resolving that difference first.
 */
class LoginThrottler
{
    public const int MAX_FAILURES_PER_EMAIL = 5;

    /**
     * Higher than the per-account limit because several people can share one public IP address.
     */
    public const int MAX_FAILURES_PER_IP = 20;

    public const int LOCK_MINUTES = 15;

    /**
     * Determine whether the address or the IP are currently locked.
     */
    public function isLocked(string $email, ?string $ipAddress): bool
    {
        return LoginThrottle::query()
            ->whereIn('key_hash', [$this->emailKey($email), $this->ipKey($ipAddress)])
            ->where('locked_until', '>', now())
            ->exists();
    }

    /**
     * Count a failed attempt, also for unknown addresses so that responses do not reveal which accounts exist.
     */
    public function recordFailure(string $email, ?string $ipAddress): void
    {
        $this->increment($this->emailKey($email), self::MAX_FAILURES_PER_EMAIL, resetsWhenIdle: false);
        $this->increment($this->ipKey($ipAddress), self::MAX_FAILURES_PER_IP, resetsWhenIdle: true);
    }

    /**
     * Restart the consecutive failures of an address after a successful login.
     *
     * The IP counter is kept, otherwise an attacker could reset it by logging in to their own account.
     */
    public function clearFailures(string $email): void
    {
        LoginThrottle::query()->where('key_hash', $this->emailKey($email))->delete();
    }

    private function increment(string $keyHash, int $maximumFailures, bool $resetsWhenIdle): void
    {
        DB::transaction(function () use ($keyHash, $maximumFailures, $resetsWhenIdle): void {
            LoginThrottle::query()->insertOrIgnore([
                'key_hash' => $keyHash,
                'failed_attempts' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $throttle = LoginThrottle::query()->where('key_hash', $keyHash)->lockForUpdate()->firstOrFail();

            $lockHasExpired = $throttle->locked_until?->isPast() ?? false;
            $isIdle = $resetsWhenIdle && $throttle->updated_at->lt(now()->subMinutes(self::LOCK_MINUTES));

            if ($lockHasExpired || $isIdle) {
                $throttle->failed_attempts = 0;
                $throttle->locked_until = null;
            }

            $throttle->failed_attempts++;

            if ($throttle->failed_attempts >= $maximumFailures) {
                $throttle->locked_until = now()->addMinutes(self::LOCK_MINUTES);
            }

            $throttle->save();
        });
    }

    private function emailKey(string $email): string
    {
        return hash('sha256', 'email|'.$email);
    }

    private function ipKey(?string $ipAddress): string
    {
        return hash('sha256', 'ip|'.$ipAddress);
    }
}
