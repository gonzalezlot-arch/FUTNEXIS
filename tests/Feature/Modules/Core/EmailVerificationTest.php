<?php

namespace Tests\Feature\Modules\Core;

use App\Models\User;
use App\Modules\Core\Actions\IssueEmailVerification;
use App\Modules\Core\Models\EmailVerificationToken;
use App\Modules\Core\Notifications\VerifyEmailNotification;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use LazilyRefreshDatabase;

    private const string PASSWORD = 'Segura#2026';

    public function test_opening_the_link_shows_the_password_form_and_does_not_consume_it(): void
    {
        [$user, $token] = $this->pendingUserWithToken();

        $this->get(route('verification.show', ['token' => $token]))
            ->assertOk()
            ->assertSee('data-password-form', false)
            ->assertSee('Un carácter especial');

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
        $this->assertDatabaseHas('email_verification_tokens', ['user_id' => $user->id]);
    }

    public function test_a_valid_link_sets_the_password_and_activates_the_account_without_logging_in(): void
    {
        [$user, $token] = $this->pendingUserWithToken();

        $this->confirm($token)->assertRedirectToRoute('login');

        $user->refresh();
        $this->assertTrue($user->hasVerifiedEmail());
        $this->assertTrue(Hash::check(self::PASSWORD, $user->password));
        $this->assertNotSame(self::PASSWORD, $user->password);
        $this->assertGuest();
    }

    public function test_the_account_can_log_in_after_setting_its_password(): void
    {
        [$user, $token] = $this->pendingUserWithToken();
        $this->confirm($token);

        $this->post(route('login'), ['email' => $user->email, 'password' => self::PASSWORD])->assertRedirectToRoute('home');

        $this->assertAuthenticatedAs($user);
    }

    public function test_a_pre_registration_gives_the_registrant_no_access(): void
    {
        Notification::fake();
        $this->post(route('register'), ['email' => 'victima@example.com', 'password' => 'Atacante#2026', 'password_confirmation' => 'Atacante#2026']);
        $victim = User::query()->where('email', 'victima@example.com')->sole();

        $this->post(route('login'), ['email' => 'victima@example.com', 'password' => 'Atacante#2026'])->assertInvalid('email');
        $this->assertGuest();

        $this->confirm($this->tokenSentTo($victim), 'Propietaria#2026');

        $this->post(route('login'), ['email' => 'victima@example.com', 'password' => 'Atacante#2026'])->assertInvalid('email');
        $this->assertGuest();

        $this->post(route('login'), ['email' => 'victima@example.com', 'password' => 'Propietaria#2026'])->assertRedirectToRoute('home');
    }

    public function test_a_used_link_is_rejected_and_cannot_change_the_password(): void
    {
        [$user, $token] = $this->pendingUserWithToken();
        $this->confirm($token);

        $this->confirm($token, 'Otra#Clave2026')->assertRedirectToRoute('verification.notice')->assertInvalid('email');
        $this->get(route('verification.show', ['token' => $token]))->assertRedirectToRoute('verification.notice');

        $this->assertTrue(Hash::check(self::PASSWORD, $user->fresh()->password));
    }

    public function test_a_link_is_valid_until_24_hours_and_expired_afterwards(): void
    {
        [$user, $token] = $this->pendingUserWithToken();
        [$secondUser, $secondToken] = $this->pendingUserWithToken();

        $this->travel(23)->hours();
        $this->confirm($secondToken)->assertValid();
        $this->assertTrue($secondUser->fresh()->hasVerifiedEmail());

        $this->travel(1)->hours();
        $this->travel(1)->seconds();
        $this->confirm($token)->assertInvalid('email');
        $this->assertFalse($user->fresh()->hasVerifiedEmail());
        $this->assertNull($user->fresh()->password);
    }

    public function test_a_new_link_invalidates_the_previous_one(): void
    {
        [$user, $firstToken] = $this->pendingUserWithToken();
        $secondToken = $this->issueToken($user);

        $this->confirm($firstToken)->assertInvalid('email');
        $this->assertFalse($user->fresh()->hasVerifiedEmail());

        $this->confirm($secondToken)->assertValid();
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }

    public function test_a_password_outside_the_policy_does_not_consume_the_link(): void
    {
        [$user, $token] = $this->pendingUserWithToken();

        $this->confirm($token, 'debil')
            ->assertRedirect(route('verification.show', ['token' => $token]))
            ->assertInvalid('password');

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
        $this->confirm($token)->assertValid();
    }

    public function test_only_the_hash_of_the_token_is_stored(): void
    {
        [$user, $token] = $this->pendingUserWithToken();

        $stored = EmailVerificationToken::query()->where('user_id', $user->id)->sole();

        $this->assertNotSame($token, $stored->token_hash);
        $this->assertSame(hash('sha256', $token), $stored->token_hash);
    }

    public function test_an_unknown_token_is_rejected(): void
    {
        $this->confirm(str_repeat('a', 64))->assertInvalid('email');
        $this->get(route('verification.show', ['token' => str_repeat('a', 64)]))->assertRedirectToRoute('verification.notice');
    }

    public function test_resend_sends_a_new_link_to_a_pending_account(): void
    {
        Notification::fake();
        $user = User::factory()->unverified()->create(['email' => 'ana@example.com']);

        $response = $this->post(route('verification.send'), ['email' => ' ANA@example.com ']);

        $response->assertSessionHas('status');
        Notification::assertSentTo($user, VerifyEmailNotification::class);
    }

    public function test_resend_does_not_send_twice_within_the_cooldown(): void
    {
        Notification::fake();
        $user = User::factory()->unverified()->create();

        $this->post(route('verification.send'), ['email' => $user->email]);
        $this->post(route('verification.send'), ['email' => $user->email]);

        Notification::assertSentToTimes($user, VerifyEmailNotification::class, 1);
    }

    public function test_resend_gives_the_same_response_for_unknown_and_verified_addresses(): void
    {
        Notification::fake();
        $verified = User::factory()->create();

        $forVerified = $this->post(route('verification.send'), ['email' => $verified->email]);
        $forUnknown = $this->post(route('verification.send'), ['email' => 'nadie@example.com']);

        $this->assertSame($forVerified->getSession()->get('status'), $forUnknown->getSession()->get('status'));
        $forUnknown->assertRedirectToRoute('verification.notice');
        Notification::assertNothingSent();
    }

    public function test_the_email_links_to_the_application_url_regardless_of_the_request_host(): void
    {
        config(['app.url' => 'https://app.futnexis.test']);
        $user = User::factory()->unverified()->create();

        $mail = (new VerifyEmailNotification('abc123'))->toMail($user);

        $this->assertSame('https://app.futnexis.test/verificar-correo/abc123', $mail->viewData['actionUrl']);
    }

    public function test_a_trailing_slash_in_the_application_url_does_not_double_the_slash(): void
    {
        config(['app.url' => 'https://app.futnexis.test/']);
        $user = User::factory()->unverified()->create();

        $mail = (new VerifyEmailNotification('abc123'))->toMail($user);

        $this->assertSame('https://app.futnexis.test/verificar-correo/abc123', $mail->viewData['actionUrl']);
    }

    private function confirm(string $token, string $password = self::PASSWORD): TestResponse
    {
        return $this->post(route('verification.confirm', ['token' => $token]), [
            'password' => $password,
            'password_confirmation' => $password,
        ]);
    }

    /**
     * @return array{User, string}
     */
    private function pendingUserWithToken(): array
    {
        $user = User::factory()->unverified()->create(['password' => null]);

        return [$user, $this->issueToken($user)];
    }

    private function issueToken(User $user): string
    {
        Notification::fake();

        app(IssueEmailVerification::class)->handle($user);

        return $this->tokenSentTo($user);
    }

    private function tokenSentTo(User $user): string
    {
        $token = null;

        Notification::assertSentTo($user, VerifyEmailNotification::class, function (VerifyEmailNotification $notification) use (&$token): bool {
            $token = $notification->token;

            return true;
        });

        return $token;
    }
}
