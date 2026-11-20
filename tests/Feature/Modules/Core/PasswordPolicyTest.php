<?php

namespace Tests\Feature\Modules\Core;

use App\Models\User;
use App\Modules\Core\Actions\IssueEmailVerification;
use App\Modules\Core\Notifications\VerifyEmailNotification;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class PasswordPolicyTest extends TestCase
{
    use LazilyRefreshDatabase;

    #[DataProvider('invalidPasswords')]
    public function test_rejects_a_password_outside_the_policy(string $password, string $message): void
    {
        $this->setPassword($password)->assertInvalid(['password' => $message]);
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
            'space is not special' => ['Segura 2026', 'La contraseña debe incluir al menos un carácter especial.'],
            'non-breaking space is not special' => ["Segura\u{00A0}2026", 'La contraseña debe incluir al menos un carácter especial.'],
            'beyond 72 bytes' => [str_repeat('Aa1#', 18).'x', 'La contraseña no puede superar los 72 bytes. Las letras acentuadas y los símbolos pueden ocupar más de uno.'],
            'beyond 72 bytes with accents' => ['Aa1#'.str_repeat('ñ', 35), 'La contraseña no puede superar los 72 bytes. Las letras acentuadas y los símbolos pueden ocupar más de uno.'],
        ];
    }

    #[TestWith(['Segura#2026'])]
    #[TestWith(['Segura 2026!'])]
    #[TestWith(['Ñandú_2026'])]
    public function test_accepts_a_password_within_the_policy(string $password): void
    {
        $this->setPassword($password)->assertValid();
    }

    public function test_accepts_a_password_of_exactly_72_bytes(): void
    {
        $this->setPassword(str_repeat('Aa1#', 18))->assertValid();
    }

    public function test_rejects_a_confirmation_that_does_not_match(): void
    {
        $this->setPassword('Segura#2026', 'Otra#Clave2026')->assertInvalid(['password' => 'Las contraseñas no coinciden.']);
    }

    public function test_requires_a_password(): void
    {
        $token = $this->token();

        $this->post(route('verification.confirm', ['token' => $token]), [])->assertInvalid(['password' => 'Ingresa una contraseña.']);
    }

    public function test_the_reset_form_applies_the_same_policy(): void
    {
        $this->post(route('password.update'), [
            'email' => 'ana@example.com',
            'token' => 'abc123',
            'password' => 'Segura 2026',
            'password_confirmation' => 'Segura 2026',
        ])->assertInvalid(['password' => 'La contraseña debe incluir al menos un carácter especial.']);
    }

    private function setPassword(string $password, ?string $confirmation = null): TestResponse
    {
        return $this->post(route('verification.confirm', ['token' => $this->token()]), [
            'password' => $password,
            'password_confirmation' => $confirmation ?? $password,
        ]);
    }

    private function token(): string
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
