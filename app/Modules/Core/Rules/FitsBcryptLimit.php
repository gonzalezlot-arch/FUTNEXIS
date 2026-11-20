<?php

namespace App\Modules\Core\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class FitsBcryptLimit implements ValidationRule
{
    /**
     * bcrypt ignores everything after this many bytes, so longer passwords are rejected instead of truncated.
     */
    public const int MAX_BYTES = 72;

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (is_string($value) && strlen($value) > self::MAX_BYTES) {
            $fail('La contraseña no puede superar los 72 bytes. Las letras acentuadas y los símbolos pueden ocupar más de uno.');
        }
    }
}
