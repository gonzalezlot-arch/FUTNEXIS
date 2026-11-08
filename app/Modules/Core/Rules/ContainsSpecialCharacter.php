<?php

namespace App\Modules\Core\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ContainsSpecialCharacter implements ValidationRule
{
    /**
     * Punctuation and symbols count; whitespace does not (D6).
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || preg_match('/[\p{P}\p{S}]/u', $value) !== 1) {
            $fail('La contraseña debe incluir al menos un carácter especial.');
        }
    }
}
