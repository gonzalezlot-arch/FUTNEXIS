<?php

namespace App\Modules\Core\Http\Requests;

class LoginRequest extends NormalizedEmailRequest
{
    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            ...$this->emailRules(),
            'password' => ['bail', 'required', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            ...$this->emailMessages(),
            'password.required' => 'Ingresa tu contraseña.',
            'password.string' => 'Ingresa tu contraseña.',
            'password.max' => 'El correo electrónico o la contraseña no son correctos.',
        ];
    }
}
