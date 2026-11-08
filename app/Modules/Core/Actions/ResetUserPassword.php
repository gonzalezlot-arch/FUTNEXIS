<?php

namespace App\Modules\Core\Actions;

use App\Models\User;
use App\Modules\Core\Enums\LoginOutcome;
use App\Modules\Core\Notifications\PasswordChangedNotification;
use App\Modules\Core\Support\AccessAudit;
use App\Modules\Core\Support\LoginThrottler;
use App\Modules\Core\Support\MailDelivery;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;

use function Illuminate\Support\defer;

class ResetUserPassword
{
    public function __construct(
        private InvalidateUserSessions $invalidateUserSessions,
        private LoginThrottler $loginThrottler,
        private AccessAudit $accessAudit,
        private MailDelivery $mailDelivery,
    ) {}

    /**
     * Apply a new password through the native broker. Returns false when the link is not valid.
     *
     * The token row stays locked during the operation, so a simultaneous request with the same
     * token waits and then finds it deleted. A completed reset also lifts the account lock (D4).
     *
     * @param  array{email: string, token: string, password: string}  $credentials
     */
    public function handle(#[\SensitiveParameter] array $credentials, ?string $ipAddress, ?string $userAgent): bool
    {
        $changedUser = null;

        $status = DB::transaction(function () use ($credentials, $ipAddress, $userAgent, &$changedUser): string {
            DB::table((string) config('auth.passwords.users.table'))
                ->where('email', $credentials['email'])
                ->lockForUpdate()
                ->first();

            return Password::broker()->reset($credentials, function (User $user, #[\SensitiveParameter] string $password) use ($ipAddress, $userAgent, &$changedUser): void {
                $user->forceFill(['password' => $password])->save();

                $this->invalidateUserSessions->handle($user);
                $this->loginThrottler->clear($this->loginThrottler->accountKey($user, $user->email));
                $this->accessAudit->record($user, $ipAddress, $userAgent, LoginOutcome::PasswordReset);

                $changedUser = $user;
            });
        });

        if ($changedUser !== null) {
            $user = $changedUser;

            defer(fn () => $this->mailDelivery->attempt('password_changed', fn () => $user->notify(new PasswordChangedNotification), $user->id));
        }

        return $status === PasswordBroker::PASSWORD_RESET;
    }
}
