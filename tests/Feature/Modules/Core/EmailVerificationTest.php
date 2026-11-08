<?php

namespace Tests\Feature\Modules\Core;

use App\Models\User;
use App\Modules\Core\Actions\IssueEmailVerification;
use App\Modules\Core\Models\EmailVerificationToken;
use App\Modules\Core\Notifications\VerifyEmailNotification;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_opening_the_link_does_not_consume_it(): void
    {
        [$user, $token] = $this->pendingUserWithToken();

        $this->get(route('verification.show', ['token' => $token]))->assertOk();

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    public function test_confirming_a_valid_link_verifies_the_account_without_logging_in(): void
    {
        [$user, $token] = $this->pendingUserWithToken();

        $response = $this->post(route('verification.confirm', ['token' => $token]));

        $response->assertRedirectToRoute('login');
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        $this->assertGuest();
    }

    public function test_a_used_link_is_rejected(): void
    {
        [, $token] = $this->pendingUserWithToken();
        $this->post(route('verification.confirm', ['token' => $token]));

        $response = $this->post(route('verification.confirm', ['token' => $token]));

        $response->assertRedirectToRoute('verification.notice')->assertInvalid('email');
    }

    public function test_a_link_is_valid_until_24_hours_and_expired_afterwards(): void
    {
        [$user, $token] = $this->pendingUserWithToken();

        $this->travel(24)->hours();
        $this->travel(1)->seconds();
        $this->post(route('verification.confirm', ['token' => $token]))->assertInvalid('email');
        $this->assertFalse($user->fresh()->hasVerifiedEmail());

        [$secondUser, $secondToken] = $this->pendingUserWithToken();
        $this->travel(23)->hours();
        $this->post(route('verification.confirm', ['token' => $secondToken]))->assertValid();
        $this->assertTrue($secondUser->fresh()->hasVerifiedEmail());
    }

    public function test_a_new_link_invalidates_the_previous_one(): void
    {
        [$user, $firstToken] = $this->pendingUserWithToken();
        $secondToken = $this->issueToken($user);

        $this->post(route('verification.confirm', ['token' => $firstToken]))->assertInvalid('email');
        $this->assertFalse($user->fresh()->hasVerifiedEmail());

        $this->post(route('verification.confirm', ['token' => $secondToken]))->assertValid();
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
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
        $this->post(route('verification.confirm', ['token' => str_repeat('a', 64)]))->assertInvalid('email');
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

        $forVerified = $this->from(route('verification.notice'))->post(route('verification.send'), ['email' => $verified->email]);
        $forUnknown = $this->from(route('verification.notice'))->post(route('verification.send'), ['email' => 'nadie@example.com']);

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

    /**
     * @return array{User, string}
     */
    private function pendingUserWithToken(): array
    {
        $user = User::factory()->unverified()->create();

        return [$user, $this->issueToken($user)];
    }

    private function issueToken(User $user): string
    {
        Notification::fake();

        app(IssueEmailVerification::class)->handle($user);

        $token = null;

        Notification::assertSentTo($user, VerifyEmailNotification::class, function (VerifyEmailNotification $notification) use (&$token): bool {
            $token = $notification->token;

            return true;
        });

        return $token;
    }
}
