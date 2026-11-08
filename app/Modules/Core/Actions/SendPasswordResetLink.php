<?php

namespace App\Modules\Core\Actions;

use Illuminate\Support\Facades\Password;

use function Illuminate\Support\defer;

class SendPasswordResetLink
{
    /**
     * Run after the response, so its duration does not reveal whether the account exists.
     */
    public function handle(string $email): void
    {
        defer(fn () => rescue(fn () => Password::broker()->sendResetLink(['email' => $email])));
    }
}
