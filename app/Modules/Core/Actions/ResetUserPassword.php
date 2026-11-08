<?php

namespace App\Modules\Core\Actions;

use App\Models\User;
use App\Modules\Core\Notifications\PasswordChangedNotification;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Support\Facades\Password;

use function Illuminate\Support\defer;

class ResetUserPassword
{
    public function __construct(private InvalidateUserSessions $invalidateUserSessions) {}

    /**
     * Apply a new password through the native broker. Returns false when the link is not valid.
     *
     * @param  array{email: string, token: string, password: string}  $credentials
     */
    public function handle(#[\SensitiveParameter] array $credentials): bool
    {
        $status = Password::broker()->reset($credentials, function (User $user, #[\SensitiveParameter] string $password): void {
            $user->forceFill(['password' => $password])->save();

            $this->invalidateUserSessions->handle($user);

            defer(fn () => rescue(fn () => $user->notify(new PasswordChangedNotification)));
        });

        return $status === PasswordBroker::PASSWORD_RESET;
    }
}
