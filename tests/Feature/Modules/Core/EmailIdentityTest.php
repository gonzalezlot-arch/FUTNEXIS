<?php

namespace Tests\Feature\Modules\Core;

use App\Models\User;
use App\Modules\Core\Actions\IssueEmailVerification;
use App\Modules\Core\Notifications\ResetPasswordNotification;
use App\Modules\Core\Notifications\VerifyEmailNotification;
use App\Modules\Core\Support\EmailAddress;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class EmailIdentityTest extends TestCase
{
    use LazilyRefreshDatabase;

    private const string PLAIN = 'jose@dominio.com';

    private const string ACCENTED = 'josé@dominio.com';

    private const string ACCENTED_NFD = "jose\u{0301}@dominio.com";

    private const string PASSWORD = 'Segura#2026';

    #[TestWith(['users'])]
    #[TestWith(['password_reset_tokens'])]
    public function test_the_email_columns_use_the_approved_collation(string $table): void
    {
        $collation = DB::selectOne(
            'SELECT COLLATION_NAME AS name FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?',
            [$table, 'email'],
        );

        $this->assertSame('utf8mb4_uca1400_as_ci', $collation->name);
    }

    public function test_users_keep_a_unique_index_on_the_address(): void
    {
        $index = DB::selectOne(
            'SELECT NON_UNIQUE AS non_unique FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?',
            ['users', 'email'],
        );

        $this->assertSame(0, (int) $index->non_unique);
    }

    #[TestWith(['  JOSÉ@Dominio.COM ', 'josé@dominio.com'])]
    #[TestWith([self::ACCENTED_NFD, 'josé@dominio.com'])]
    #[TestWith(['USUARIO@DOMINIO.COM', 'usuario@dominio.com'])]
    public function test_normalization_trims_composes_and_lowercases_without_removing_accents(string $input, string $expected): void
    {
        $this->assertSame($expected, EmailAddress::normalize($input));
    }

    public function test_invalid_utf8_normalizes_to_an_empty_address(): void
    {
        $this->assertSame('', EmailAddress::normalize("jose\xFF@dominio.com"));
    }

    public function test_accented_and_plain_addresses_can_register_as_separate_accounts(): void
    {
        Notification::fake();

        $this->post(route('register'), ['email' => self::PLAIN])->assertValid();
        $this->post(route('register'), ['email' => self::ACCENTED])->assertValid();

        $this->assertDatabaseCount('users', 2);
    }

    public function test_verified_accented_and_plain_accounts_coexist(): void
    {
        $plain = $this->account(self::PLAIN);
        $accented = $this->account(self::ACCENTED);

        $this->assertNotSame($plain->id, $accented->id);
        $this->assertSame([self::ACCENTED, self::PLAIN], User::query()->orderByDesc('id')->pluck('email')->all());
    }

    public function test_decomposed_and_uppercase_spellings_of_a_verified_address_are_duplicates(): void
    {
        $this->account(self::ACCENTED);

        $this->post(route('register'), ['email' => self::ACCENTED_NFD])->assertInvalid('email');
        $this->post(route('register'), ['email' => 'JOSÉ@DOMINIO.COM'])->assertInvalid('email');

        $this->assertDatabaseCount('users', 1);
    }

    public function test_the_address_is_stored_composed(): void
    {
        Notification::fake();

        $this->post(route('register'), ['email' => self::ACCENTED_NFD]);

        $this->assertSame(self::ACCENTED, User::query()->sole()->email);
    }

    public function test_the_password_of_one_identity_does_not_open_the_other(): void
    {
        $this->account(self::PLAIN);
        $accented = $this->account(self::ACCENTED, 'Distinta#2026');

        $this->login(self::ACCENTED, self::PASSWORD)->assertInvalid('email');
        $this->assertGuest();

        $this->login(self::ACCENTED_NFD, 'Distinta#2026')->assertRedirectToRoute('home');
        $this->assertAuthenticatedAs($accented);
    }

    public function test_an_address_without_account_does_not_reach_its_accent_variant(): void
    {
        $this->account(self::PLAIN);

        $this->login(self::ACCENTED, self::PASSWORD)->assertInvalid('email');

        $this->assertGuest();
    }

    public function test_locking_one_identity_does_not_lock_the_other(): void
    {
        $this->account(self::PLAIN);
        $accented = $this->account(self::ACCENTED);
        foreach (range(1, 5) as $attempt) {
            $this->login(self::PLAIN, 'Incorrecta#1');
        }

        $this->login(self::ACCENTED)->assertRedirectToRoute('home');

        $this->assertAuthenticatedAs($accented);
    }

    public function test_a_locked_account_rejects_its_decomposed_and_uppercase_spellings(): void
    {
        $this->account(self::ACCENTED);
        foreach (range(1, 5) as $attempt) {
            $this->login(self::ACCENTED, 'Incorrecta#1');
        }

        $this->login(self::ACCENTED_NFD)->assertInvalid('email');
        $this->login('JOSÉ@dominio.com')->assertInvalid('email');

        $this->assertGuest();
    }

    public function test_recovery_of_an_accented_account_reaches_only_that_account(): void
    {
        Notification::fake();
        $plain = $this->account(self::PLAIN);
        $accented = $this->account(self::ACCENTED);

        $this->post(route('password.email'), ['email' => self::ACCENTED]);

        Notification::assertSentTo($accented, ResetPasswordNotification::class);
        Notification::assertNotSentTo($plain, ResetPasswordNotification::class);
    }

    public function test_the_reset_token_of_one_identity_cannot_reset_the_other(): void
    {
        $plain = $this->account(self::PLAIN);
        $this->account(self::ACCENTED, 'Original#2026');
        $tokenOfOtherAccount = Password::broker()->createToken($plain);

        $this->post(route('password.update'), [
            'email' => self::ACCENTED,
            'token' => $tokenOfOtherAccount,
            'password' => 'Robada#2026',
            'password_confirmation' => 'Robada#2026',
        ])->assertInvalid('email');

        $this->assertTrue(Hash::check('Original#2026', User::query()->where('email', self::ACCENTED)->value('password')));
    }

    public function test_requesting_a_reset_for_one_identity_keeps_the_token_of_the_other(): void
    {
        $plain = $this->account(self::PLAIN);
        $accented = $this->account(self::ACCENTED);

        Password::broker()->createToken($plain);
        Password::broker()->createToken($accented);

        $this->assertDatabaseCount('password_reset_tokens', 2);
    }

    public function test_verifying_an_accented_account_activates_only_that_account(): void
    {
        Notification::fake();
        $plain = User::factory()->unverified()->create(['email' => self::PLAIN, 'password' => null]);
        $accented = User::factory()->unverified()->create(['email' => self::ACCENTED, 'password' => null]);
        app(IssueEmailVerification::class)->handle($accented);

        $token = null;
        Notification::assertSentTo($accented, VerifyEmailNotification::class, function (VerifyEmailNotification $notification) use (&$token): bool {
            $token = $notification->token;

            return true;
        });

        $this->post(route('verification.confirm', ['token' => $token]), ['password' => self::PASSWORD, 'password_confirmation' => self::PASSWORD]);

        $this->assertTrue($accented->fresh()->hasVerifiedEmail());
        $this->assertFalse($plain->fresh()->hasVerifiedEmail());
    }

    private function account(string $email, string $password = self::PASSWORD): User
    {
        return User::factory()->create(['email' => $email, 'password' => $password]);
    }

    private function login(string $email, string $password = self::PASSWORD): TestResponse
    {
        $this->app['auth']->forgetGuards();

        return $this->post(route('login'), ['email' => $email, 'password' => $password]);
    }
}
