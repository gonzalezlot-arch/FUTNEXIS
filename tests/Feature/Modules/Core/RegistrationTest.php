<?php

namespace Tests\Feature\Modules\Core;

use App\Models\User;
use App\Modules\Core\Actions\RegisterUser;
use App\Modules\Core\Notifications\VerifyEmailNotification;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_valid_address_creates_pending_account_without_password_or_session(): void
    {
        Notification::fake();

        $response = $this->post(route('register'), ['email' => 'ana@example.com']);

        $response->assertRedirectToRoute('verification.notice');
        $this->assertGuest();

        $user = User::query()->where('email', 'ana@example.com')->firstOrFail();
        $this->assertNull($user->name);
        $this->assertNull($user->password);
        $this->assertNull($user->email_verified_at);
        Notification::assertSentTo($user, VerifyEmailNotification::class);
    }

    public function test_a_password_sent_with_the_registration_is_ignored(): void
    {
        Notification::fake();

        $this->post(route('register'), ['email' => 'ana@example.com', 'password' => 'Atacante#2026', 'password_confirmation' => 'Atacante#2026']);

        $this->assertNull(User::query()->where('email', 'ana@example.com')->value('password'));
    }

    public function test_stores_the_address_trimmed_and_lowercased(): void
    {
        Notification::fake();

        $this->post(route('register'), ['email' => '  ANA.Perez@Example.COM ']);

        $this->assertDatabaseHas('users', ['email' => 'ana.perez@example.com']);
    }

    #[DataProvider('duplicateAddresses')]
    public function test_rejects_an_address_of_a_verified_account_with_the_approved_message(string $duplicate): void
    {
        Notification::fake();
        User::factory()->create(['email' => 'ana@example.com']);

        $response = $this->post(route('register'), ['email' => $duplicate]);

        $response->assertInvalid(['email' => 'Este correo electrónico ya se encuentra registrado. Inicia sesión o recupera tu contraseña']);
        $this->assertDatabaseCount('users', 1);
        Notification::assertNothingSent();
    }

    /**
     * @return array<string, array{string}>
     */
    public static function duplicateAddresses(): array
    {
        return [
            'identical' => ['ana@example.com'],
            'different case' => ['ANA@Example.com'],
            'outer spaces' => ['   ana@example.com   '],
        ];
    }

    public function test_a_pending_address_can_be_requested_again_so_nobody_can_reserve_it(): void
    {
        Notification::fake();
        $this->post(route('register'), ['email' => 'ana@example.com']);
        $this->travel(2)->minutes();

        $response = $this->post(route('register'), ['email' => 'ana@example.com']);

        $response->assertRedirectToRoute('verification.notice')->assertValid();
        $this->assertDatabaseCount('users', 1);
        Notification::assertSentToTimes(User::query()->sole(), VerifyEmailNotification::class, 2);
    }

    public function test_a_second_request_within_the_cooldown_sends_no_second_email(): void
    {
        Notification::fake();
        $this->post(route('register'), ['email' => 'ana@example.com']);

        $this->post(route('register'), ['email' => 'ana@example.com']);

        Notification::assertSentToTimes(User::query()->sole(), VerifyEmailNotification::class, 1);
    }

    public function test_the_database_rejects_a_duplicate_that_skips_validation(): void
    {
        User::factory()->create(['email' => 'ana@example.com']);

        $this->expectException(UniqueConstraintViolationException::class);

        User::factory()->create(['email' => 'ANA@EXAMPLE.COM ']);
    }

    public function test_a_verified_duplicate_reaching_the_action_shows_the_approved_message(): void
    {
        Notification::fake();
        User::factory()->create(['email' => 'ana@example.com']);

        try {
            app(RegisterUser::class)->handle('ana@example.com');
            $this->fail('The duplicate should have been rejected.');
        } catch (ValidationException $exception) {
            $this->assertSame(RegisterUser::DUPLICATE_EMAIL_MESSAGE, $exception->errors()['email'][0]);
        }
    }

    public function test_rejects_a_malformed_address(): void
    {
        $response = $this->post(route('register'), ['email' => 'no-es-un-correo']);

        $response->assertInvalid(['email' => 'Ingresa un correo electrónico válido.']);
    }

    public function test_requires_an_address(): void
    {
        $this->post(route('register'), [])->assertInvalid(['email' => 'Ingresa tu correo electrónico.']);
    }

    public function test_ignores_attributes_outside_the_form(): void
    {
        Notification::fake();

        $this->post(route('register'), ['email' => 'ana@example.com', 'email_verified_at' => now()->toDateTimeString(), 'name' => 'Intruso']);

        $user = User::query()->where('email', 'ana@example.com')->firstOrFail();
        $this->assertNull($user->email_verified_at);
        $this->assertNull($user->name);
    }

    public function test_redirects_an_authenticated_user_away_from_the_form(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('register'))
            ->assertRedirectToRoute('home');
    }

    public function test_the_forms_show_no_password_at_registration_and_no_remember_option(): void
    {
        $this->get(route('login'))->assertOk()->assertDontSee('name="remember"', false);

        $this->get(route('register'))->assertOk()->assertDontSee('name="password"', false);
    }

    public function test_a_recovery_burst_does_not_exhaust_the_registration_limit(): void
    {
        Notification::fake();

        foreach (range(1, 11) as $attempt) {
            $this->post(route('password.email'), ['email' => "p{$attempt}@example.com"]);
        }

        $this->post(route('register'), ['email' => 'ana@example.com'])->assertValid();
        $this->assertDatabaseHas('users', ['email' => 'ana@example.com']);
    }

    public function test_the_form_limit_shows_a_visible_message(): void
    {
        Notification::fake();

        foreach (range(1, 10) as $attempt) {
            $this->post(route('register'), ['email' => "p{$attempt}@example.com"]);
        }

        $this->from(route('register'))->followingRedirects()
            ->post(route('register'), ['email' => 'ultimo@example.com'])
            ->assertOk()
            ->assertSee('Demasiadas solicitudes seguidas');

        $this->assertDatabaseMissing('users', ['email' => 'ultimo@example.com']);
    }
}
