<?php

namespace App\Modules\Core\Support;

use Closure;
use Illuminate\Support\Facades\Log;
use Throwable;

class MailDelivery
{
    /**
     * Run a mail-sending operation and log its failure for operators.
     *
     * Only the exception class is logged: its message can contain addresses and SMTP dialogue.
     */
    public function attempt(string $purpose, Closure $send, ?int $userId = null): bool
    {
        try {
            $send();

            return true;
        } catch (Throwable $exception) {
            Log::error('core.mail.delivery_failed', [
                'purpose' => $purpose,
                'user_id' => $userId,
                'exception' => $exception::class,
            ]);

            return false;
        }
    }
}
