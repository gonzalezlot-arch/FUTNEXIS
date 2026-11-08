<?php

namespace Tests\Feature\Modules\Core;

use App\Models\User;
use App\Modules\Core\Models\LoginAttempt;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class InputRobustnessTest extends TestCase
{
    use LazilyRefreshDatabase;

    /**
     * @param  array<string, mixed>  $payload
     */
    #[DataProvider('malformedSubmissions')]
    public function test_malformed_submissions_are_rejected_without_a_server_error(string $route, string $form, array $payload): void
    {
        Notification::fake();

        $this->from(route($form))->followingRedirects()->post(route($route), $payload)->assertOk();

        $this->assertDatabaseCount('users', 0);
    }

    /**
     * @return array<string, array{string, string, array<string, mixed>}>
     */
    public static function malformedSubmissions(): array
    {
        return [
            'register array email' => ['register', 'register', ['email' => ['x']]],
            'register invalid utf-8' => ['register', 'register', ['email' => "ana\xFF@example.com"]],
            'register unexpected invalid field' => ['register', 'register', ['email' => 'mal', 'extra' => "\xFF"]],
            'login array email' => ['login', 'login', ['email' => ['x'], 'password' => 'Segura#2026']],
            'login array password' => ['login', 'login', ['email' => 'ana@example.com', 'password' => ['x']]],
            'login invalid utf-8' => ['login', 'login', ['email' => "ana\xFF@example.com", 'password' => "\xFF"]],
            'recovery array email' => ['password.email', 'password.request', ['email' => ['x']]],
            'recovery invalid utf-8' => ['password.email', 'password.request', ['email' => "\xFF"]],
            'resend array email' => ['verification.send', 'verification.notice', ['email' => ['x']]],
            'reset array fields' => ['password.update', 'password.request', ['email' => ['x'], 'token' => ['y'], 'password' => ['z']]],
            'reset invalid utf-8 token' => ['password.update', 'password.request', ['email' => 'ana@example.com', 'token' => "\xFF", 'password' => 'Segura#2026', 'password_confirmation' => 'Segura#2026']],
        ];
    }

    public function test_malformed_submissions_survive_the_database_session_driver(): void
    {
        config(['session.driver' => 'database']);

        $this->from(route('login'))->followingRedirects()
            ->post(route('login'), ['email' => "ana\xFF@example.com", 'password' => 'x', 'extra' => "\xFF"])
            ->assertOk();
    }

    #[TestWith(['/restablecer-contrasena/abc?email[]=x'])]
    #[TestWith(['/restablecer-contrasena/abc?email=%FF'])]
    public function test_the_reset_form_tolerates_a_malformed_email_query(string $url): void
    {
        $this->get($url)->assertOk()->assertSee('name="email"', false)->assertSee('value=""', false);
    }

    #[TestWith(['/restablecer-contrasena/%FF'])]
    #[TestWith(['/verificar-correo/%FF'])]
    public function test_a_malformed_token_in_the_address_is_a_client_error(string $url): void
    {
        $this->get($url)->assertClientError();
    }

    public function test_a_malformed_user_agent_does_not_break_the_login_and_is_stored_clean(): void
    {
        $user = User::factory()->create(['password' => 'Segura#2026']);

        $this->withHeader('User-Agent', "Mozilla\xFF\xFE\x01/5.0")
            ->post(route('login'), ['email' => $user->email, 'password' => 'Segura#2026'])
            ->assertRedirectToRoute('home');

        $this->assertTrue(mb_check_encoding((string) LoginAttempt::query()->sole()->user_agent, 'UTF-8'));
        $this->assertStringNotContainsString("\x01", (string) LoginAttempt::query()->sole()->user_agent);
    }

    public function test_a_very_long_user_agent_is_truncated_to_the_column(): void
    {
        $user = User::factory()->create(['password' => 'Segura#2026']);

        $this->withHeader('User-Agent', str_repeat('ñ', 2000))
            ->post(route('login'), ['email' => $user->email, 'password' => 'Segura#2026'])
            ->assertRedirectToRoute('home');

        $this->assertSame(512, mb_strlen((string) LoginAttempt::query()->sole()->user_agent));
    }

    public function test_an_address_longer_than_the_column_is_rejected(): void
    {
        $address = str_repeat('a', 250).'@example.com';

        $this->post(route('register'), ['email' => $address])
            ->assertInvalid(['email' => 'El correo electrónico no puede superar los 255 caracteres.']);
    }

    public function test_a_very_long_password_at_login_gets_the_generic_error(): void
    {
        $user = User::factory()->create();

        $this->post(route('login'), ['email' => $user->email, 'password' => str_repeat('a', 5000)])->assertInvalid('password');

        $this->assertGuest();
    }
}
