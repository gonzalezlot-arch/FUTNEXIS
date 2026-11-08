<?php

namespace App\Modules\Core\Actions;

use App\Models\User;
use App\Modules\Core\Enums\LoginOutcome;
use App\Modules\Core\Models\LoginAttempt;
use App\Modules\Core\Support\LoginThrottler;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthenticateUser
{
    public const string INVALID_CREDENTIALS_MESSAGE = 'El correo electrónico o la contraseña no son correctos.';

    public const string LOCKED_MESSAGE = 'Por seguridad, el acceso se bloqueó temporalmente tras varios intentos fallidos. Inténtalo de nuevo más tarde.';

    public const string UNVERIFIED_MESSAGE = 'Debes verificar tu correo electrónico antes de iniciar sesión.';

    public function __construct(private LoginThrottler $loginThrottler) {}

    /**
     * Validate the credentials and the account state, applying the attempt limits.
     *
     * @throws ValidationException
     */
    public function handle(string $email, #[\SensitiveParameter] string $password, ?string $ipAddress, ?string $userAgent): User
    {
        if ($this->loginThrottler->isLocked($email, $ipAddress)) {
            $this->recordAttempt(null, $ipAddress, $userAgent, LoginOutcome::Locked);

            throw ValidationException::withMessages(['email' => self::LOCKED_MESSAGE]);
        }

        $user = User::query()->where('email', $email)->first();

        if (! $this->passwordMatches($user, $password)) {
            $this->rejectInvalidCredentials($user, $email, $ipAddress, $userAgent);
        }

        if (! $user->hasVerifiedEmail()) {
            $this->recordAttempt($user, $ipAddress, $userAgent, LoginOutcome::Unverified);

            throw ValidationException::withMessages(['email' => self::UNVERIFIED_MESSAGE]);
        }

        $this->loginThrottler->clearFailures($email);
        $this->recordAttempt($user, $ipAddress, $userAgent, LoginOutcome::Succeeded);

        return $user;
    }

    /**
     * Hash also for unknown addresses, so response times do not reveal which accounts exist.
     */
    private function passwordMatches(?User $user, #[\SensitiveParameter] string $password): bool
    {
        if ($user === null) {
            Hash::make($password);

            return false;
        }

        return Hash::check($password, $user->password);
    }

    /**
     * @throws ValidationException
     */
    private function rejectInvalidCredentials(?User $user, string $email, ?string $ipAddress, ?string $userAgent): never
    {
        $this->loginThrottler->recordFailure($email, $ipAddress);
        $this->recordAttempt($user, $ipAddress, $userAgent, LoginOutcome::InvalidCredentials);

        throw ValidationException::withMessages([
            'email' => $this->loginThrottler->isLocked($email, $ipAddress)
                ? self::LOCKED_MESSAGE
                : self::INVALID_CREDENTIALS_MESSAGE,
        ]);
    }

    private function recordAttempt(?User $user, ?string $ipAddress, ?string $userAgent, LoginOutcome $outcome): void
    {
        LoginAttempt::query()->create([
            'user_id' => $user?->id,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent === null ? null : Str::limit($userAgent, 512, ''),
            'outcome' => $outcome,
            'attempted_at' => now(),
        ]);
    }
}
