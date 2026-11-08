<?php

namespace App\Modules\Core\Actions;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class InvalidateUserSessions
{
    /**
     * Invalidate every session of the user, on any session driver (RN-CORE-005, RN-CORE-009).
     *
     * Raising the epoch rejects existing sessions on their next request. With the database driver
     * the stored rows are removed as well, so their data does not outlive the session.
     */
    public function handle(User $user): void
    {
        $user->increment('session_epoch');

        if (config('session.driver') !== 'database') {
            return;
        }

        DB::connection(config('session.connection'))
            ->table(config('session.table'))
            ->where('user_id', $user->id)
            ->delete();
    }
}
