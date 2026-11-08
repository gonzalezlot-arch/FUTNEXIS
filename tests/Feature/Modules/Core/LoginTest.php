<?php

namespace Tests\Feature\Modules\Core;

use App\Models\User;
use App\Modules\Core\Actions\AuthenticateUser;
use App\Modules\Core\Enums\LoginOutcome;
use App\Modules\Core\Models\LoginAttempt;
use App\Modules\Core\Models\LoginThrottle;
use App\Modules\Core\Support\LoginThrottler;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use LazilyRefreshDatabase;

    private const string PASSWORD = 'Segura#2026';

    public function test_a_verified_user_logs_in_and_reaches_home(): void
    {
        $user = $this->verifiedUser();

        $this->login($user->email)->assertRedirectToRoute('home');

        $this->assertAuthenticatedAs($user);
        $this->get(route('home'))->assertOk()->assertSee('Crear una organización')->assertSee('Aceptar una invitación');
    }

    public function test_the_address_is_normalized_at_login(): void
    {
        $user = $this->verifiedUser(['email' => 'ana@example.com']);

        $this->login('  ANA@Example.com ')->assertRedirectToRoute('home');

        $this->assertAuthenticatedAs($user);
    }

    public function test_an_unverified_user_cannot_log_in(): void
    {
        $user = User::factory()->unverified()->create(['password' => self::PASSWORD]);

        $this->login($user->email)->assertInvalid(['email' => AuthenticateUser::UNVERIFIED_MESSAGE]);

        $this->assertGuest();
    }

    public function test_wrong_password_and_unknown_address_get_the_same_message(): void
    {
        $user = $this->verifiedUser();

        $this->login($user->email, 'Incorrecta#1')->assertInvalid(['email' => AuthenticateUser::INVALID_CREDENTIALS_MESSAGE]);
        $this->login('nadie@example.com', 'Incorrecta#1')->assertInvalid(['email' => AuthenticateUser::INVALID_CREDENTIALS_MESSAGE]);

        $this->assertGuest();
    }

    public function test_five_consecutive_failures_lock_the_account_even_with_the_right_password(): void
    {
        $user = $this->verifiedUser();

        $this->failLogin($user->email, 4);
        $this->login($user->email, 'Incorrecta#1')->assertInvalid(['email' => AuthenticateUser::LOCKED_MESSAGE]);

        $this->login($user->email)->assertInvalid(['email' => AuthenticateUser::LOCKED_MESSAGE]);
        $this->assertGuest();
    }

    public function test_four_failures_do_not_lock_the_account(): void
    {
        $user = $this->verifiedUser();

        $this->failLogin($user->email, 4);

        $this->login($user->email)->assertRedirectToRoute('home');
    }

    public function test_the_lock_lasts_15_minutes(): void
    {
        $user = $this->verifiedUser();
        $this->failLogin($user->email, 5);

        $this->travel(14)->minutes();
        $this->login($user->email)->assertInvalid(['email' => AuthenticateUser::LOCKED_MESSAGE]);

        $this->travel(1)->minutes();
        $this->travel(1)->seconds();
        $this->login($user->email)->assertRedirectToRoute('home');
    }

    public function test_a_failure_after_the_lock_expires_starts_a_new_count(): void
    {
        $user = $this->verifiedUser();
        $this->failLogin($user->email, 5);
        $this->travel(16)->minutes();

        $this->login($user->email, 'Incorrecta#1')->assertInvalid(['email' => AuthenticateUser::INVALID_CREDENTIALS_MESSAGE]);

        $this->login($user->email)->assertRedirectToRoute('home');
    }

    public function test_a_successful_login_resets_the_counter(): void
    {
        $user = $this->verifiedUser();
        $this->failLogin($user->email, 4);
        $this->login($user->email)->assertRedirectToRoute('home');
        $this->post(route('logout'));

        $this->failLogin($user->email, 4);

        $this->login($user->email)->assertRedirectToRoute('home');
    }

    public function test_unknown_addresses_are_locked_like_real_ones(): void
    {
        $this->failLogin('nadie@example.com', 4);

        $this->login('nadie@example.com', 'Incorrecta#1')->assertInvalid(['email' => AuthenticateUser::LOCKED_MESSAGE]);
    }

    public function test_the_lock_of_one_account_does_not_affect_another(): void
    {
        $locked = $this->verifiedUser();
        $other = $this->verifiedUser();
        $this->failLogin($locked->email, 5);

        $this->login($other->email)->assertRedirectToRoute('home');
    }

    public function test_a_correct_password_is_accepted_from_an_ip_over_its_limit(): void
    {
        $user = $this->verifiedUser();
        $this->exceedIpLimit();

        $this->login($user->email)->assertRedirectToRoute('home');

        $this->assertAuthenticatedAs($user);
    }

    public function test_a_wrong_password_from_an_ip_over_its_limit_gets_the_network_message(): void
    {
        $user = $this->verifiedUser();
        $this->exceedIpLimit();

        $this->login($user->email, 'Incorrecta#1')->assertInvalid(['email' => AuthenticateUser::IP_LIMITED_MESSAGE]);

        $this->assertGuest();
    }

    public function test_the_ip_limit_never_replaces_the_account_lock(): void
    {
        $user = $this->verifiedUser();
        $this->exceedIpLimit();
        $this->failLogin($user->email, 5);

        $this->login($user->email)->assertInvalid(['email' => AuthenticateUser::LOCKED_MESSAGE]);

        $this->assertGuest();
    }

    public function test_slow_failures_from_a_shared_ip_expire_with_the_window(): void
    {
        foreach (range(1, LoginThrottler::IP_FAILURE_THRESHOLD - 1) as $attempt) {
            app(LoginThrottler::class)->recordIpFailure('127.0.0.1');
        }

        $this->travel(LoginThrottler::IP_WINDOW_MINUTES + 1)->minutes();
        app(LoginThrottler::class)->recordIpFailure('127.0.0.1');

        $this->assertFalse(app(LoginThrottler::class)->isIpOverLimit('127.0.0.1'));
    }

    public function test_another_ip_is_not_affected_by_the_ip_limit(): void
    {
        $this->exceedIpLimit();

        $this->assertFalse(app(LoginThrottler::class)->isIpOverLimit('203.0.113.9'));
    }

    public function test_a_successful_login_does_not_reset_the_ip_counter(): void
    {
        $user = $this->verifiedUser();
        $this->exceedIpLimit();

        $this->login($user->email)->assertRedirectToRoute('home');

        $this->assertTrue(app(LoginThrottler::class)->isIpOverLimit('127.0.0.1'));
    }

    public function test_unknown_addresses_from_an_ip_over_its_limit_write_nothing(): void
    {
        $this->exceedIpLimit();
        $throttles = LoginThrottle::query()->count();

        $this->login('nadie@example.com', 'Incorrecta#1')->assertInvalid(['email' => AuthenticateUser::IP_LIMITED_MESSAGE]);

        $this->assertSame($throttles, LoginThrottle::query()->count());
        $this->assertDatabaseCount('login_attempts', 0);
    }

    public function test_requests_while_locked_write_nothing_and_the_lock_keeps_its_account(): void
    {
        $user = $this->verifiedUser();
        $this->failLogin($user->email, 5);
        $attempts = LoginAttempt::query()->count();

        $this->failLogin($user->email, 10);

        $this->assertSame($attempts, LoginAttempt::query()->count());
        $this->assertDatabaseHas('login_attempts', ['user_id' => $user->id, 'outcome' => LoginOutcome::Locked->value]);
    }

    public function test_a_locked_account_rejects_every_spelling_that_reaches_it(): void
    {
        $user = $this->verifiedUser(['email' => 'jose@example.com']);
        $this->failLogin('jose@example.com', 5);

        $this->login('JOSE@example.com')->assertInvalid(['email' => AuthenticateUser::LOCKED_MESSAGE]);
        $this->login('  Jose@Example.COM ')->assertInvalid(['email' => AuthenticateUser::LOCKED_MESSAGE]);

        $this->assertGuest();
        $this->assertTrue(app(LoginThrottler::class)->isLocked(app(LoginThrottler::class)->accountKey($user, 'otra-forma@example.com')));
    }

    public function test_a_password_beyond_the_bcrypt_limit_does_not_match_a_stored_prefix(): void
    {
        $password = str_repeat('Aa1#', 18);
        $user = $this->verifiedUser(['password' => $password]);

        $this->login($user->email, $password.'sobrante')->assertInvalid(['email' => AuthenticateUser::INVALID_CREDENTIALS_MESSAGE]);

        $this->assertGuest();
        $this->login($user->email, $password)->assertRedirectToRoute('home');
    }

    public function test_a_pending_account_without_password_cannot_log_in(): void
    {
        $user = User::factory()->unverified()->create(['password' => null]);

        $this->login($user->email, 'Cualquiera#2026')->assertInvalid(['email' => AuthenticateUser::INVALID_CREDENTIALS_MESSAGE]);

        $this->assertGuest();
    }

    public function test_every_attempt_records_ip_and_device(): void
    {
        $user = $this->verifiedUser();

        $this->withHeader('User-Agent', 'FutnexisTest/1.0')->login($user->email, 'Incorrecta#1');
        $this->withHeader('User-Agent', 'FutnexisTest/1.0')->login($user->email);

        $attempts = LoginAttempt::query()->orderBy('id')->get();

        $this->assertSame([LoginOutcome::InvalidCredentials, LoginOutcome::Succeeded], $attempts->pluck('outcome')->all());
        $this->assertSame('FutnexisTest/1.0', $attempts->last()->user_agent);
        $this->assertSame('127.0.0.1', $attempts->last()->ip_address);
        $this->assertSame($user->id, $attempts->last()->user_id);
    }

    public function test_a_remember_option_is_ignored(): void
    {
        $user = $this->verifiedUser();
        $originalRememberToken = $user->remember_token;

        $response = $this->post(route('login'), ['email' => $user->email, 'password' => self::PASSWORD, 'remember' => 'on']);

        $this->assertSame($originalRememberToken, $user->fresh()->remember_token);
        foreach ($response->headers->getCookies() as $cookie) {
            $this->assertStringNotContainsString('remember_', $cookie->getName());
        }
    }

    public function test_login_requires_email_and_password(): void
    {
        $this->post(route('login'), [])->assertInvalid(['email', 'password']);

        $this->assertDatabaseCount('login_throttles', 0);
    }

    public function test_home_redirects_guests_to_login(): void
    {
        $this->get(route('home'))->assertRedirectToRoute('login');
    }

    public function test_an_unverified_session_cannot_reach_home(): void
    {
        $this->actingAs(User::factory()->unverified()->create())
            ->get(route('home'))
            ->assertRedirectToRoute('verification.notice');
    }

    public function test_logout_ends_the_session(): void
    {
        $this->actingAs($this->verifiedUser());

        $this->post(route('logout'))->assertRedirectToRoute('login');

        $this->assertGuest();
    }

    public function test_logout_is_not_available_to_guests(): void
    {
        $this->post(route('logout'))->assertRedirectToRoute('login');
    }

    /**
     * @param  array<string, string>  $attributes
     */
    private function verifiedUser(array $attributes = []): User
    {
        return User::factory()->create(['password' => self::PASSWORD, ...$attributes]);
    }

    private function login(string $email, string $password = self::PASSWORD): TestResponse
    {
        return $this->post(route('login'), ['email' => $email, 'password' => $password]);
    }

    private function exceedIpLimit(): void
    {
        foreach (range(1, LoginThrottler::IP_FAILURE_THRESHOLD) as $attempt) {
            app(LoginThrottler::class)->recordIpFailure('127.0.0.1');
        }
    }

    private function failLogin(string $email, int $times): void
    {
        foreach (range(1, $times) as $attempt) {
            $this->login($email, 'Incorrecta#1');
        }
    }
}
