<?php

namespace App\Modules\Core\Support;

use Normalizer;

class EmailAddress
{
    /**
     * Normalize an address for storage and comparison: outer spaces, Unicode NFC and lowercase.
     *
     * Accents are preserved. Invalid UTF-8 becomes an empty string, so validation rejects it.
     */
    public static function normalize(string $email): string
    {
        if (! mb_check_encoding($email, 'UTF-8')) {
            return '';
        }

        $composed = Normalizer::normalize(trim($email), Normalizer::FORM_C);

        if ($composed === false) {
            return '';
        }

        return Normalizer::normalize(mb_strtolower($composed, 'UTF-8'), Normalizer::FORM_C) ?: '';
    }
}
