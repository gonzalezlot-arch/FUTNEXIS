<?php

namespace Tests\Feature\Modules\Core;

use App\Models\User;
use App\Modules\Core\Notifications\PasswordChangedNotification;
use App\Modules\Core\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use LazilyRefreshDatabase;

    private const string NEW_PASSWORD = 'Nueva#Clave2026';

    public function test_requesting_a_link_emails_it_to_the_user(): void
    {
        Notification::fake();
        $user = User::factory()->create(['email' => 'ana@example.com']);

        $this->post(route('password.email'), ['email' => ' ANA@example.com '])->assertSessionHas('status');

        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }

    public function test_unknown_and_known_addresses_get_the_same_response(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $forKnown = $this->from(route('password.request'))->post(route('password.email'), ['email' => $user->email]);
        $forUnknown = $this->from(route('password.request'))->post(route('password.email'), ['email' => 'nadie@example.com']);

        $this->assertSame($forKnown->getSession()->get('status'), $forUnknown->getSession()->get('status'));
        $forUnknown->assertRedirectToRoute('password.request')->assertValid();
    }

    public function test_the_token_is_stored_hashed(): void
    {
        $user = User::factory()->create();
        $token = Password::broker()->createToken($user);

        $stored = DB::table('password_reset_tokens')->where('email', $user->email)->value('token');

        $this->assertNotSame($token, $stored);
        $this->assertTrue(Hash::check($token, $stored));
    }

    public function test_a_valid_link_changes_the_password_and_notifies_the_user(): void
    {
        Notification::fake();
        $user = User::factory()->create();
        $token = Password::broker()->createToken($user);

        $this->resetPassword($user->email, $token)->assertRedirectToRoute('login');

        $this->assertTrue(Hash::check(self::NEW_PASSWORD, $user->fresh()->password));
        $this->assertGuest();
        Notification::assertSentTo($user, PasswordChangedNotification::class);
    }

    public function test_a_link_can_only_be_used_once(): void
    {
        Notification::fake();
        $user = User::factory()->create();
        $token = Password::broker()->createToken($user);
        $this->resetPassword($user->email, $token);

        $this->resetPassword($user->email, $token, 'Otra#Clave2026')->assertInvalid('email');

        $this->assertTrue(Hash::check(self::NEW_PASSWORD, $user->fresh()->password));
    }

    public function test_a_link_expires_after_60_minutes(): void
    {
        $user = User::factory()->create(['password' => 'Original#2026']);
        $token = Password::broker()->createToken($user);

        $this->travel(61)->minutes();

        $this->resetPassword($user->email, $token)->assertInvalid('email');
        $this->assertTrue(Hash::check('Original#2026', $user->fresh()->password));
    }

    public function test_a_link_is_accepted_within_60_minutes(): void
    {
        Notification::fake();
        $user = User::factory()->create();
        $token = Password::broker()->createToken($user);

        $this->travel(59)->minutes();

        $this->resetPassword($user->email, $token)->assertRedirectToRoute('login');
    }

    public function test_a_new_link_invalidates_the_previous_one(): void
    {
        Notification::fake();
        $user = User::factory()->create(['password' => 'Original#2026']);
        $firstToken = Password::broker()->createToken($user);
        $secondToken = Password::broker()->createToken($user);

        $this->resetPassword($user->email, $firstToken)->assertInvalid('email');
        $this->assertTrue(Hash::check('Original#2026', $user->fresh()->password));

        $this->resetPassword($user->email, $secondToken)->assertRedirectToRoute('login');
    }

    public function test_the_new_password_must_follow_the_policy(): void
    {
        $user = User::factory()->create(['password' => 'Original#2026']);
        $token = Password::broker()->createToken($user);

        $this->resetPassword($user->email, $token, 'debil')->assertInvalid('password');

        $this->assertTrue(Hash::check('Original#2026', $user->fresh()->password));
    }

    public function test_resetting_removes_every_stored_session_of_the_user_only(): void
    {
        Notification::fake();
        config(['session.driver' => 'database']);
        $user = User::factory()->create();
        $other = User::factory()->create();
        $this->storeSession('a', $user);
        $this->storeSession('b', $user);
        $this->storeSession('c', $other);
        $token = Password::broker()->createToken($user);

        $this->resetPassword($user->email, $token);

        $this->assertDatabaseMissing('sessions', ['user_id' => $user->id]);
        $this->assertDatabaseHas('sessions', ['id' => 'c']);
    }

    public function test_the_reset_email_links_to_the_application_url(): void
    {
        config(['app.url' => 'https://app.futnexis.test']);
        $user = User::factory()->create(['email' => 'ana@example.com']);

        $mail = (new ResetPasswordNotification('abc123'))->toMail($user);

        $this->assertSame('https://app.futnexis.test/restablecer-contrasena/abc123?email=ana%40example.com', $mail->viewData['actionUrl']);
    }

    private function resetPassword(string $email, string $token, string $password = self::NEW_PASSWORD): TestResponse
    {
        return $this->post(route('password.update'), [
            'email' => $email,
            'token' => $token,
            'password' => $password,
            'password_confirmation' => $password,
        ]);
    }

    private function storeSession(string $id, User $user): void
    {
        DB::table('sessions')->insert([
            'id' => $id,
            'user_id' => $user->id,
            'payload' => '',
            'last_activity' => now()->getTimestamp(),
        ]);
    }
}
