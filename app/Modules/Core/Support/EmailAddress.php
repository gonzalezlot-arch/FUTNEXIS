<?php

namespace App\Modules\Core\Support;

class EmailAddress
{
    /**
     * Normalize an address for storage and comparison (RN-CORE-001).
     */
    public static function normalize(string $email): string
    {
        return mb_strtolower(trim($email), 'UTF-8');
    }
}
