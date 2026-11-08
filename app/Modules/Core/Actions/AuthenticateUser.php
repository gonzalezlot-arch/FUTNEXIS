<?php

namespace App\Modules\Core\Actions;

use App\Models\User;
use App\Modules\Core\Enums\LoginOutcome;
use App\Modules\Core\Exceptions\LoginThrottleUnavailable;
use App\Modules\Core\Rules\FitsBcryptLimit;
use App\Modules\Core\Support\AccessAudit;
use App\Modules\Core\Support\LoginThrottler;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthenticateUser
{
    public const string INVALID_CREDENTIALS_MESSAGE = 'El correo electrónico o la contraseña no son correctos.';

    public const string LOCKED_MESSAGE = 'Por seguridad, el acceso se bloqueó temporalmente tras varios intentos fallidos. Espera 15 minutos o recupera tu contraseña.';

    public const string IP_LIMITED_MESSAGE = 'Detectamos demasiados intentos fallidos desde tu red. Revisa tus datos o inténtalo de nuevo más tarde.';

    public const string BUSY_MESSAGE = 'No pudimos procesar tu solicitud en este momento. Inténtalo de nuevo.';

    public const string UNVERIFIED_MESSAGE = 'Debes verificar tu correo electrónico antes de iniciar sesión.';

    public function __construct(
        private LoginThrottler $loginThrottler,
        private AccessAudit $accessAudit,
    ) {}

    /**
     * Validate the credentials and the account state, applying the attempt limits.
     *
     * @throws ValidationException
     */
    public function handle(string $email, #[\SensitiveParameter] string $password, ?string $ipAddress, ?string $userAgent): User
    {
        $user = User::query()->where('email', $email)->first();
        $accountKey = $this->loginThrottler->accountKey($user, $email);
        $ipIsOverLimit = $this->loginThrottler->isIpOverLimit($ipAddress);

        $this->reserveAttempt($user, $password, $accountKey, $ipIsOverLimit);

        if (! $this->passwordMatches($user, $password)) {
            $this->rejectInvalidCredentials($user, $accountKey, $ipIsOverLimit, $ipAddress, $userAgent);
        }

        $this->loginThrottler->clear($accountKey);

        if (! $user->hasVerifiedEmail()) {
            $this->accessAudit->record($user, $ipAddress, $userAgent, LoginOutcome::Unverified);

            throw ValidationException::withMessages(['email' => self::UNVERIFIED_MESSAGE]);
        }

        $this->accessAudit->record($user, $ipAddress, $userAgent, LoginOutcome::Succeeded);

        return $user;
    }

    /**
     * Reject without writing when the account is already locked; the event was recorded when the lock began.
     * An address with no account behind it cannot be a correct password, so an over-limit IP stops here as well.
     *
     * @throws ValidationException
     */
    private function reserveAttempt(?User $user, #[\SensitiveParameter] string $password, string $accountKey, bool $ipIsOverLimit): void
    {
        if ($this->loginThrottler->isLocked($accountKey)) {
            throw ValidationException::withMessages(['email' => self::LOCKED_MESSAGE]);
        }

        if ($user === null && $ipIsOverLimit) {
            Hash::make($password);

            throw ValidationException::withMessages(['email' => self::IP_LIMITED_MESSAGE]);
        }

        try {
            $allowed = $this->loginThrottler->reserveAttempt($accountKey);
        } catch (LoginThrottleUnavailable) {
            throw ValidationException::withMessages(['email' => self::BUSY_MESSAGE]);
        }

        if (! $allowed) {
            throw ValidationException::withMessages(['email' => self::LOCKED_MESSAGE]);
        }
    }

    /**
     * Hash also for unknown or pending accounts, so response times do not reveal which accounts exist.
     * Passwords beyond the bcrypt limit never match, because their final part would not be verified.
     */
    private function passwordMatches(?User $user, #[\SensitiveParameter] string $password): bool
    {
        if ($user?->password === null || strlen($password) > FitsBcryptLimit::MAX_BYTES) {
            Hash::make($password);

            return false;
        }

        return Hash::check($password, $user->password);
    }

    /**
     * @throws ValidationException
     */
    private function rejectInvalidCredentials(?User $user, string $accountKey, bool $ipIsOverLimit, ?string $ipAddress, ?string $userAgent): never
    {
        $this->loginThrottler->recordIpFailure($ipAddress);

        $isLocked = $this->loginThrottler->isLocked($accountKey);

        $this->accessAudit->record($user, $ipAddress, $userAgent, $isLocked ? LoginOutcome::Locked : LoginOutcome::InvalidCredentials);

        throw ValidationException::withMessages(['email' => match (true) {
            $isLocked => self::LOCKED_MESSAGE,
            $ipIsOverLimit => self::IP_LIMITED_MESSAGE,
            default => self::INVALID_CREDENTIALS_MESSAGE,
        }]);
    }
}
