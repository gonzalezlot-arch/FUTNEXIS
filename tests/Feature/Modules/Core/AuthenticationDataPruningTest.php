<?php

namespace Tests\Feature\Modules\Core;

use App\Models\User;
use App\Modules\Core\Actions\PruneAuthenticationData;
use App\Modules\Core\Models\EmailVerificationToken;
use App\Modules\Core\Models\LoginAttempt;
use App\Modules\Core\Models\LoginThrottle;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Lottery;
use Tests\TestCase;

class AuthenticationDataPruningTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_login_attempts_are_deleted_after_their_retention_period(): void
    {
        $this->attemptAt(now()->subDays(PruneAuthenticationData::LOGIN_ATTEMPT_RETENTION_DAYS + 1));
        $recent = $this->attemptAt(now()->subDays(PruneAuthenticationData::LOGIN_ATTEMPT_RETENTION_DAYS - 1));

        app(PruneAuthenticationData::class)->handle();

        $this->assertSame([$recent->id], LoginAttempt::query()->pluck('id')->all());
    }

    public function test_expired_locks_are_deleted_and_active_ones_are_kept(): void
    {
        $this->throttle('expired', lockedUntil: now()->subHours(PruneAuthenticationData::EXPIRED_LOCK_RETENTION_HOURS + 1));
        $this->throttle('active', lockedUntil: now()->addMinutes(5));

        app(PruneAuthenticationData::class)->handle();

        $this->assertSame([$this->hash('active')], LoginThrottle::query()->pluck('key_hash')->all());
    }

    public function test_idle_counters_are_deleted_and_recent_ones_are_kept(): void
    {
        $this->throttle('idle', updatedAt: now()->subDays(PruneAuthenticationData::IDLE_COUNTER_RETENTION_DAYS + 1));
        $this->throttle('recent', updatedAt: now()->subDays(PruneAuthenticationData::IDLE_COUNTER_RETENTION_DAYS - 1));

        app(PruneAuthenticationData::class)->handle();

        $this->assertSame([$this->hash('recent')], LoginThrottle::query()->pluck('key_hash')->all());
    }

    public function test_expired_verification_tokens_are_deleted(): void
    {
        $expired = User::factory()->unverified()->create(['password' => null]);
        $valid = User::factory()->unverified()->create(['password' => null]);
        EmailVerificationToken::query()->create(['user_id' => $expired->id, 'token_hash' => str_repeat('a', 64), 'expires_at' => now()->subMinute()]);
        EmailVerificationToken::query()->create(['user_id' => $valid->id, 'token_hash' => str_repeat('b', 64), 'expires_at' => now()->addHour()]);

        app(PruneAuthenticationData::class)->handle();

        $this->assertSame([$valid->id], EmailVerificationToken::query()->pluck('user_id')->all());
    }

    public function test_expired_reset_tokens_are_deleted(): void
    {
        DB::table('password_reset_tokens')->insert([
            ['email' => 'vencido@example.com', 'token' => 'x', 'created_at' => now()->subMinutes(61)],
            ['email' => 'vigente@example.com', 'token' => 'y', 'created_at' => now()->subMinutes(59)],
        ]);

        app(PruneAuthenticationData::class)->handle();

        $this->assertSame(['vigente@example.com'], DB::table('password_reset_tokens')->pluck('email')->all());
    }

    public function test_unverified_accounts_are_deleted_after_their_retention_period(): void
    {
        $old = User::factory()->unverified()->create(['password' => null, 'created_at' => now()->subDays(PruneAuthenticationData::UNVERIFIED_ACCOUNT_RETENTION_DAYS + 1)]);
        $recent = User::factory()->unverified()->create(['password' => null]);

        app(PruneAuthenticationData::class)->handle();

        $this->assertDatabaseMissing('users', ['id' => $old->id]);
        $this->assertDatabaseHas('users', ['id' => $recent->id]);
    }

    public function test_verified_accounts_are_never_deleted(): void
    {
        $user = User::factory()->create(['created_at' => now()->subYears(2)]);

        app(PruneAuthenticationData::class)->handle();

        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }

    public function test_deleting_a_user_keeps_its_attempts_without_the_personal_link(): void
    {
        $user = User::factory()->unverified()->create(['password' => null, 'created_at' => now()->subDays(PruneAuthenticationData::UNVERIFIED_ACCOUNT_RETENTION_DAYS + 1)]);
        $attempt = $this->attemptAt(now(), $user);

        app(PruneAuthenticationData::class)->handle();

        $this->assertNull($attempt->fresh()->user_id);
    }

    public function test_the_command_prunes_and_reports(): void
    {
        $this->attemptAt(now()->subDays(PruneAuthenticationData::LOGIN_ATTEMPT_RETENTION_DAYS + 1));

        $this->artisan('core:prune-auth-data')->assertSuccessful()->expectsOutputToContain('login_attempts');

        $this->assertDatabaseCount('login_attempts', 0);
    }

    public function test_the_cleanup_is_scheduled_daily(): void
    {
        $events = collect(app(Schedule::class)->events())->filter(fn ($event): bool => $event->description === 'core:prune-auth-data');

        $this->assertCount(1, $events);
        $this->assertSame('0 0 * * *', $events->first()->expression);
    }

    public function test_a_login_can_trigger_the_cleanup_without_a_cron_job(): void
    {
        Lottery::alwaysWin();
        $this->attemptAt(now()->subDays(PruneAuthenticationData::LOGIN_ATTEMPT_RETENTION_DAYS + 1));

        try {
            $this->post(route('login'), ['email' => 'nadie@example.com', 'password' => 'Incorrecta#1']);
        } finally {
            Lottery::determineResultNormally();
        }

        $this->assertSame(0, LoginAttempt::query()->where('attempted_at', '<', now()->subDays(1))->count());
    }

    private function attemptAt(Carbon $date, ?User $user = null): LoginAttempt
    {
        return LoginAttempt::query()->create([
            'user_id' => $user?->id,
            'ip_address' => '203.0.113.1',
            'user_agent' => 'Test',
            'outcome' => 'invalid_credentials',
            'attempted_at' => $date,
        ]);
    }

    private function throttle(string $name, ?Carbon $lockedUntil = null, ?Carbon $updatedAt = null): void
    {
        DB::table('login_throttles')->insert([
            'key_hash' => $this->hash($name),
            'failed_attempts' => 1,
            'locked_until' => $lockedUntil,
            'created_at' => $updatedAt ?? now(),
            'updated_at' => $updatedAt ?? now(),
        ]);
    }

    private function hash(string $name): string
    {
        return hash('sha256', $name);
    }
}
