<?php

namespace App\Modules\Core\Http\Requests;

use App\Modules\Core\Support\EmailAddress;
use Illuminate\Foundation\Http\FormRequest;

abstract class NormalizedEmailRequest extends FormRequest
{
    /**
     * Normalize the address before any rule evaluates it.
     */
    protected function prepareForValidation(): void
    {
        $email = $this->input('email');

        if (is_string($email)) {
            $this->merge(['email' => EmailAddress::normalize($email)]);
        }
    }

    /**
     * @return array<string, array<int, string>>
     */
    protected function emailRules(): array
    {
        return ['email' => ['required', 'string', 'email', 'max:255']];
    }

    /**
     * @return array<string, string>
     */
    protected function emailMessages(): array
    {
        return [
            'email.required' => 'Ingresa tu correo electrónico.',
            'email.string' => 'Ingresa un correo electrónico válido.',
            'email.email' => 'Ingresa un correo electrónico válido.',
            'email.max' => 'El correo electrónico no puede superar los 255 caracteres.',
        ];
    }
}
