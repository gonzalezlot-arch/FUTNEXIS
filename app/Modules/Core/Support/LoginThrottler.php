<?php

namespace App\Modules\Core\Support;

use App\Models\User;
use App\Modules\Core\Exceptions\LoginThrottleUnavailable;
use App\Modules\Core\Models\LoginThrottle;
use Closure;
use Illuminate\Database\ConcurrencyErrorDetector;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Applies the login attempt limits.
 *
 * The account limit is the primary control: five consecutive failures lock the account for 15 minutes.
 * The IP limit is secondary: it never rejects a correct password. Device data is audit information only and
 * establishes no lock (DEC-CORE-005, D8).
 */
class LoginThrottler
{
    public const int MAX_FAILURES_PER_ACCOUNT = 5;

    public const int LOCK_MINUTES = 15;

    /**
     * Tolerant on purpose: clubs, venues and mobile networks place many people behind one address.
     */
    public const int IP_FAILURE_THRESHOLD = 100;

    public const int IP_WINDOW_MINUTES = 15;

    private const int TRANSACTION_ATTEMPTS = 5;

    /**
     * Identify the counter by the account found, so any spelling that reaches the same account shares it.
     * Unknown addresses get their own counter, so responses do not reveal which accounts exist.
     */
    public function accountKey(?User $user, string $email): string
    {
        return $this->key($user === null ? 'email|'.$email : 'account|'.$user->id);
    }

    public function isLocked(string $accountKey): bool
    {
        return LoginThrottle::query()
            ->where('key_hash', $accountKey)
            ->where('locked_until', '>', now())
            ->exists();
    }

    /**
     * Count an attempt before checking the password, so simultaneous requests cannot exceed the limit.
     * Returns false when the attempt must be rejected without evaluating the password.
     *
     * @throws LoginThrottleUnavailable
     */
    public function reserveAttempt(string $accountKey): bool
    {
        return $this->withLockedRow($accountKey, function (LoginThrottle $throttle): bool {
            if ($throttle->locked_until?->isFuture()) {
                return false;
            }

            if ($throttle->locked_until !== null) {
                $throttle->failed_attempts = 0;
                $throttle->locked_until = null;
            }

            $throttle->failed_attempts++;

            if ($throttle->failed_attempts >= self::MAX_FAILURES_PER_ACCOUNT) {
                $throttle->locked_until = now()->addMinutes(self::LOCK_MINUTES);
            }

            $throttle->save();

            return true;
        });
    }

    /**
     * Forget the failures of an account after a correct password or a completed password reset.
     */
    public function clear(string $accountKey): void
    {
        LoginThrottle::query()->where('key_hash', $accountKey)->delete();
    }

    public function isIpOverLimit(?string $ipAddress): bool
    {
        return LoginThrottle::query()
            ->where('key_hash', $this->ipKey($ipAddress))
            ->where('failed_attempts', '>=', self::IP_FAILURE_THRESHOLD)
            ->where('created_at', '>=', now()->subMinutes(self::IP_WINDOW_MINUTES))
            ->exists();
    }

    /**
     * Count a failure against the address within a fixed window. A correct password does not clear it,
     * otherwise an attacker could reset the counter by logging in to their own account.
     */
    public function recordIpFailure(?string $ipAddress): void
    {
        try {
            $failures = $this->withLockedRow($this->ipKey($ipAddress), function (LoginThrottle $throttle): int {
                if ($throttle->created_at->lt(now()->subMinutes(self::IP_WINDOW_MINUTES))) {
                    $throttle->failed_attempts = 0;
                    $throttle->created_at = now();
                }

                $throttle->failed_attempts++;
                $throttle->save();

                return $throttle->failed_attempts;
            });
        } catch (LoginThrottleUnavailable) {
            return;
        }

        if ($failures === self::IP_FAILURE_THRESHOLD) {
            Log::warning('core.login.ip_threshold_reached', ['ip_bucket' => IpAddress::bucket($ipAddress)]);
        }
    }

    /**
     * Serialize on one row: inserting with ON DUPLICATE KEY takes an exclusive lock from the start,
     * which avoids the shared-to-exclusive upgrade that makes concurrent requests deadlock.
     *
     * @template TResult
     *
     * @param  Closure(LoginThrottle): TResult  $callback
     * @return TResult
     *
     * @throws LoginThrottleUnavailable
     */
    private function withLockedRow(string $keyHash, Closure $callback): mixed
    {
        try {
            return DB::transaction(function () use ($keyHash, $callback): mixed {
                LoginThrottle::query()->upsert([['key_hash' => $keyHash, 'failed_attempts' => 0]], ['key_hash'], ['key_hash']);

                return $callback(LoginThrottle::query()->where('key_hash', $keyHash)->lockForUpdate()->firstOrFail());
            }, self::TRANSACTION_ATTEMPTS);
        } catch (QueryException $exception) {
            if ((new ConcurrencyErrorDetector)->causedByConcurrencyError($exception)) {
                throw new LoginThrottleUnavailable(previous: $exception);
            }

            throw $exception;
        }
    }

    private function ipKey(?string $ipAddress): string
    {
        return $this->key('ip|'.IpAddress::bucket($ipAddress));
    }

    private function key(string $identifier): string
    {
        return hash_hmac('sha256', $identifier, (string) config('app.key'));
    }
}
