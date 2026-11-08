<?php

namespace App\Modules\Core\Actions;

use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Validation\ValidationException;

use function Illuminate\Support\defer;

class RegisterUser
{
    public const string DUPLICATE_EMAIL_MESSAGE = 'Este correo electrónico ya se encuentra registrado. Inicia sesión o recupera tu contraseña';

    public function __construct(private IssueEmailVerification $issueEmailVerification) {}

    /**
     * Create a pending account. The email is sent after the response, so a mail failure does not lose the account
     * and the user can still request a new link.
     *
     * @throws ValidationException
     */
    public function handle(string $email, #[\SensitiveParameter] string $password): User
    {
        try {
            $user = User::query()->create(['email' => $email, 'password' => $password]);
        } catch (UniqueConstraintViolationException) {
            throw ValidationException::withMessages(['email' => self::DUPLICATE_EMAIL_MESSAGE]);
        }

        defer(fn () => rescue(fn () => $this->issueEmailVerification->handle($user)));

        return $user;
    }
}
