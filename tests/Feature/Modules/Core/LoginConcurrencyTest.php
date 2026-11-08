<?php

namespace Tests\Feature\Modules\Core;

use App\Models\User;
use App\Modules\Core\Actions\AuthenticateUser;
use App\Modules\Core\Enums\LoginOutcome;
use App\Modules\Core\Models\LoginAttempt;
use App\Modules\Core\Models\LoginThrottle;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Illuminate\Process\Pool;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Process;
use PDOException;
use Tests\TestCase;

class LoginConcurrencyTest extends TestCase
{
    use DatabaseTruncation;

    private const string PASSWORD = 'Segura#2026';

    /**
     * These tests commit real rows, because each process has its own connection.
     */
    protected function tearDown(): void
    {
        foreach (['login_attempts', 'login_throttles', 'password_reset_tokens', 'users'] as $table) {
            DB::table($table)->delete();
        }

        parent::tearDown();
    }

    public function test_simultaneous_requests_cannot_evaluate_more_than_five_passwords(): void
    {
        $user = User::factory()->create(['email' => 'conc@example.com', 'password' => self::PASSWORD]);

        $results = $this->runSimultaneously(12, 'login', 'conc@example.com', 'Incorrecta#1');

        $this->assertNoProcessFailed($results);
        $this->assertSame(
            5,
            LoginAttempt::query()->where('user_id', $user->id)
                ->whereIn('outcome', [LoginOutcome::InvalidCredentials, LoginOutcome::Locked])
                ->count(),
        );
        $this->assertTrue(LoginThrottle::query()->where('locked_until', '>', now())->exists());
    }

    public function test_simultaneous_use_of_one_reset_token_succeeds_once(): void
    {
        $user = User::factory()->create(['email' => 'reset@example.com']);
        $token = Password::broker()->createToken($user);

        $results = $this->runSimultaneously(8, 'reset', 'reset@example.com', $token);

        $this->assertNoProcessFailed($results);
        $this->assertSame(1, substr_count($results, 'reset'));
    }

    public function test_a_deadlock_is_retried_and_the_attempt_is_counted_once(): void
    {
        $user = User::factory()->create(['email' => 'retry@example.com', 'password' => self::PASSWORD]);
        $this->failNextThrottleWrites(1);

        $this->post(route('login'), ['email' => 'retry@example.com', 'password' => 'Incorrecta#1'])
            ->assertInvalid(['email' => AuthenticateUser::INVALID_CREDENTIALS_MESSAGE]);

        $this->assertSame(1, (int) LoginThrottle::query()->orderBy('id')->value('failed_attempts'));
        $this->assertDatabaseHas('login_attempts', ['user_id' => $user->id]);
    }

    public function test_persistent_deadlocks_fail_closed_without_a_server_error(): void
    {
        User::factory()->create(['email' => 'busy@example.com', 'password' => self::PASSWORD]);
        $this->failNextThrottleWrites(PHP_INT_MAX);

        $this->post(route('login'), ['email' => 'busy@example.com', 'password' => self::PASSWORD])
            ->assertRedirect()
            ->assertInvalid(['email' => AuthenticateUser::BUSY_MESSAGE]);

        $this->assertGuest();
    }

    private function failNextThrottleWrites(int $failures): void
    {
        DB::beforeExecuting(function (string $query, array $bindings, $connection) use (&$failures): void {
            if ($failures > 0 && str_starts_with($query, 'insert into `login_throttles`')) {
                $failures--;

                throw new QueryException(
                    $connection->getName(),
                    $query,
                    $bindings,
                    new PDOException('SQLSTATE[40001]: Serialization failure: 1213 Deadlock found when trying to get lock; try restarting transaction'),
                );
            }
        });
    }

    private function runSimultaneously(int $processes, string $operation, string $email, string $secret): string
    {
        $startEpoch = (string) (microtime(true) + 4);
        $script = base_path('tests/Fixtures/concurrent_auth_operation.php');

        $results = Process::pool(function (Pool $pool) use ($processes, $operation, $startEpoch, $email, $secret, $script): void {
            foreach (range(1, $processes) as $index) {
                $pool->as((string) $index)->path(base_path())->timeout(60)->env([
                    'APP_ENV' => 'testing',
                    'DB_CONNECTION' => 'mariadb',
                    'DB_DATABASE' => 'futnexis_testing',
                    'MAIL_MAILER' => 'array',
                    'LOG_CHANNEL' => 'null',
                    'BCRYPT_ROUNDS' => '8',
                ])->command([PHP_BINARY, $script, $operation, $startEpoch, $email, $secret]);
            }
        })->start()->wait();

        return collect($results->collect())->map(fn ($result): string => $result->output().$result->errorOutput())->implode('');
    }

    private function assertNoProcessFailed(string $results): void
    {
        $this->assertStringNotContainsString('error ', $results);
        $this->assertStringNotContainsString('Refusing', $results);
    }
}
