<?php

namespace App\Modules\Core\Http\Requests\Concerns;

trait HasPasswordPolicyMessages
{
    /**
     * @return array<string, string>
     */
    protected function passwordPolicyMessages(): array
    {
        return [
            'password.required' => 'Ingresa una contraseña.',
            'password.string' => 'La contraseña no es válida.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.password.mixed' => 'La contraseña debe incluir al menos una mayúscula y una minúscula.',
            'password.password.numbers' => 'La contraseña debe incluir al menos un número.',
        ];
    }
}
