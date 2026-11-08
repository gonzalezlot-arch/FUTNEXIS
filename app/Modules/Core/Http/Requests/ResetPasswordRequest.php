<?php

namespace App\Modules\Core\Http\Requests;

use App\Modules\Core\Http\Requests\Concerns\HasPasswordPolicyMessages;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rules\Password;

class ResetPasswordRequest extends NormalizedEmailRequest
{
    use HasPasswordPolicyMessages;

    /**
     * @return array<string, array<int, ValidationRule|string>>
     */
    public function rules(): array
    {
        return [
            ...$this->emailRules(),
            'token' => ['required', 'string'],
            'password' => ['required', 'string', 'confirmed', 'max:255', Password::defaults()],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            ...$this->emailMessages(),
            ...$this->passwordPolicyMessages(),
            'token.required' => 'El enlace de restablecimiento no es válido.',
        ];
    }
}
