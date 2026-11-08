<?php

namespace Tests\Feature\Modules\Core;

use App\Models\User;
use App\Modules\Core\Actions\RegisterUser;
use App\Modules\Core\Notifications\VerifyEmailNotification;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use LazilyRefreshDatabase;

    private const string VALID_PASSWORD = 'Segura#2026';

    public function test_valid_payload_creates_pending_account_without_logging_in(): void
    {
        Notification::fake();

        $response = $this->post(route('register'), $this->payload());

        $response->assertRedirectToRoute('verification.notice');
        $this->assertGuest();

        $user = User::query()->where('email', 'ana@example.com')->firstOrFail();
        $this->assertNull($user->name);
        $this->assertNull($user->email_verified_at);
        $this->assertNotSame(self::VALID_PASSWORD, $user->password);
        $this->assertTrue(Hash::check(self::VALID_PASSWORD, $user->password));
        Notification::assertSentTo($user, VerifyEmailNotification::class);
    }

    public function test_stores_the_address_trimmed_and_lowercased(): void
    {
        Notification::fake();

        $this->post(route('register'), $this->payload(['email' => '  ANA.Perez@Example.COM ']));

        $this->assertDatabaseHas('users', ['email' => 'ana.perez@example.com']);
    }

    #[DataProvider('duplicateAddresses')]
    public function test_rejects_a_duplicate_address_with_the_approved_message(string $duplicate): void
    {
        Notification::fake();
        User::factory()->create(['email' => 'ana@example.com']);

        $response = $this->post(route('register'), $this->payload(['email' => $duplicate]));

        $response->assertInvalid(['email' => 'Este correo electrónico ya se encuentra registrado. Inicia sesión o recupera tu contraseña']);
        $this->assertDatabaseCount('users', 1);
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

    public function test_the_database_rejects_a_duplicate_that_skips_validation(): void
    {
        User::factory()->create(['email' => 'ana@example.com']);

        $this->expectException(UniqueConstraintViolationException::class);

        User::factory()->create(['email' => 'ANA@EXAMPLE.COM ']);
    }

    public function test_a_concurrent_duplicate_shows_the_approved_message(): void
    {
        Notification::fake();
        User::factory()->create(['email' => 'ana@example.com']);

        try {
            app(RegisterUser::class)->handle('ana@example.com', self::VALID_PASSWORD);
            $this->fail('The duplicate should have been rejected.');
        } catch (ValidationException $exception) {
            $this->assertSame(RegisterUser::DUPLICATE_EMAIL_MESSAGE, $exception->errors()['email'][0]);
        }
    }

    #[DataProvider('invalidPasswords')]
    public function test_rejects_a_password_outside_the_policy(string $password, string $message): void
    {
        $response = $this->post(route('register'), $this->payload(['password' => $password, 'password_confirmation' => $password]));

        $response->assertInvalid(['password' => $message]);
        $this->assertDatabaseCount('users', 0);
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function invalidPasswords(): array
    {
        return [
            'too short' => ['Ab1#xyz', 'La contraseña debe tener al menos 8 caracteres.'],
            'no uppercase' => ['segura#2026', 'La contraseña debe incluir al menos una mayúscula y una minúscula.'],
            'no lowercase' => ['SEGURA#2026', 'La contraseña debe incluir al menos una mayúscula y una minúscula.'],
            'no number' => ['Segura#Clave', 'La contraseña debe incluir al menos un número.'],
            'no special character' => ['Segura2026', 'La contraseña debe incluir al menos un carácter especial.'],
        ];
    }

    public function test_rejects_a_confirmation_that_does_not_match(): void
    {
        $response = $this->post(route('register'), $this->payload(['password_confirmation' => 'Otra#Clave2026']));

        $response->assertInvalid(['password' => 'Las contraseñas no coinciden.']);
    }

    public function test_rejects_a_malformed_address(): void
    {
        $response = $this->post(route('register'), $this->payload(['email' => 'no-es-un-correo']));

        $response->assertInvalid(['email' => 'Ingresa un correo electrónico válido.']);
    }

    public function test_requires_email_and_password(): void
    {
        $response = $this->post(route('register'), []);

        $response->assertInvalid(['email' => 'Ingresa tu correo electrónico.', 'password' => 'Ingresa una contraseña.']);
    }

    public function test_ignores_attributes_outside_the_form(): void
    {
        Notification::fake();

        $this->post(route('register'), $this->payload(['email_verified_at' => now()->toDateTimeString(), 'name' => 'Intruso']));

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

    public function test_the_form_renders_the_password_policy_without_a_remember_option(): void
    {
        $this->get(route('login'))->assertOk()->assertDontSee('name="remember"', false);

        $this->get(route('register'))
            ->assertOk()
            ->assertSee('data-password-form', false)
            ->assertSee('Un carácter especial');
    }

    /**
     * @param  array<string, string>  $overrides
     * @return array<string, string>
     */
    private function payload(array $overrides = []): array
    {
        return [
            'email' => 'ana@example.com',
            'password' => self::VALID_PASSWORD,
            'password_confirmation' => self::VALID_PASSWORD,
            ...$overrides,
        ];
    }
}
