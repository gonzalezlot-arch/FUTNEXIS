<?php

namespace App\Modules\Core\Actions;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class InvalidateUserSessions
{
    /**
     * Remove every stored session of the user (RN-CORE-005).
     *
     * Other session drivers cannot be queried by user; for them the "auth.session" middleware
     * logs out any session whose stored password hash no longer matches.
     */
    public function handle(User $user): void
    {
        if (config('session.driver') !== 'database') {
            return;
        }

        DB::connection(config('session.connection'))
            ->table(config('session.table'))
            ->where('user_id', $user->id)
            ->delete();
    }
}
