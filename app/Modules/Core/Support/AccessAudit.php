<?php

namespace App\Modules\Core\Support;

use App\Models\User;
use App\Modules\Core\Enums\LoginOutcome;
use App\Modules\Core\Models\LoginAttempt;
use Illuminate\Support\Str;

class AccessAudit
{
    private const int MAX_USER_AGENT_LENGTH = 512;

    /**
     * Keep the event with its account, address and device. The device is audit data only.
     */
    public function record(?User $user, ?string $ipAddress, ?string $userAgent, LoginOutcome $outcome): void
    {
        LoginAttempt::query()->create([
            'user_id' => $user?->id,
            'ip_address' => $ipAddress,
            'user_agent' => $this->sanitizeUserAgent($userAgent),
            'outcome' => $outcome,
            'attempted_at' => now(),
        ]);
    }

    /**
     * The header is client-controlled: invalid UTF-8 would otherwise make the insert fail.
     */
    private function sanitizeUserAgent(?string $userAgent): ?string
    {
        if ($userAgent === null) {
            return null;
        }

        $printable = preg_replace('/[\x00-\x1F\x7F]/', '', mb_scrub($userAgent, 'UTF-8')) ?? '';

        return Str::substr($printable, 0, self::MAX_USER_AGENT_LENGTH);
    }
}
