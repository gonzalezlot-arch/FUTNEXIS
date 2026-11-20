<?php

namespace Tests\Feature\Modules\Core;

use App\Models\User;
use App\Modules\Core\Actions\IssueEmailVerification;
use App\Modules\Core\Notifications\VerifyEmailNotification;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class SessionTokenSecrecyTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['session.driver' => 'database']);
    }

    public function test_a_reset_token_is_not_readable_in_the_stored_session(): void
    {
        $user = User::factory()->create(['email' => 'victima@example.com']);
        $token = Password::broker()->createToken($user);

        $this->get(route('password.reset', ['token' => $token, 'email' => $user->email]))->assertOk();

        $this->assertSessionDoesNotExpose($token, 'victima');
    }

    public function test_a_verification_token_is_not_readable_in_the_stored_session(): void
    {
        $token = $this->verificationToken();

        $this->get(route('verification.show', ['token' => $token]))->assertOk();

        $this->assertSessionDoesNotExpose($token);
    }

    public function test_a_failed_reset_does_not_keep_the_token_as_old_input(): void
    {
        $user = User::factory()->create();
        $token = Password::broker()->createToken($user);

        $this->post(route('password.update'), ['email' => $user->email, 'token' => $token, 'password' => 'debil', 'password_confirmation' => 'debil'])
            ->assertRedirect(route('password.reset', ['token' => $token]))
            ->assertInvalid('password');

        $this->assertSessionDoesNotExpose($token);
    }

    public function test_a_failed_activation_returns_to_the_form_without_keeping_the_token(): void
    {
        $token = $this->verificationToken();

        $this->post(route('verification.confirm', ['token' => $token]), ['password' => 'debil', 'password_confirmation' => 'debil'])
            ->assertRedirect(route('verification.show', ['token' => $token]));

        $this->assertSessionDoesNotExpose($token);
    }

    public function test_pages_with_a_token_cannot_be_cached(): void
    {
        $response = $this->get(route('verification.show', ['token' => $this->verificationToken()]));

        $this->assertStringContainsString('no-store', (string) $response->headers->get('Cache-Control'));
    }

    public function test_tokens_are_not_written_to_the_log_during_the_flows(): void
    {
        $token = $this->verificationToken();
        $logged = [];
        Log::listen(function ($event) use (&$logged): void {
            $logged[] = $event->message.json_encode($event->context);
        });

        $this->get(route('verification.show', ['token' => $token]));
        $this->post(route('verification.confirm', ['token' => $token]), ['password' => 'debil', 'password_confirmation' => 'debil']);

        $this->assertStringNotContainsString($token, implode(' ', $logged));
    }

    /**
     * The raw row must not reveal the value, and neither must the decrypted session.
     */
    private function assertSessionDoesNotExpose(string ...$values): void
    {
        $raw = (string) DB::table('sessions')->orderByDesc('last_activity')->value('payload');
        $decoded = (string) base64_decode($raw);
        $decrypted = (string) app('encrypter')->decrypt($decoded, false);

        $this->assertStringContainsString('_token', $decrypted);

        foreach ($values as $value) {
            $this->assertStringNotContainsString($value, $raw);
            $this->assertStringNotContainsString($value, $decoded);
            $this->assertStringNotContainsString($value, $decrypted);
        }
    }

    private function verificationToken(): string
    {
        Notification::fake();
        $user = User::factory()->unverified()->create(['password' => null]);

        app(IssueEmailVerification::class)->handle($user);

        $token = null;

        Notification::assertSentTo($user, VerifyEmailNotification::class, function (VerifyEmailNotification $notification) use (&$token): bool {
            $token = $notification->token;

            return true;
        });

        return $token;
    }
}
