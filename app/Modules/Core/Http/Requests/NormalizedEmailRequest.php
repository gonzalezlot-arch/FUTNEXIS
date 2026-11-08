<?php

namespace App\Modules\Core\Http\Requests;

use App\Modules\Core\Support\EmailAddress;
use Illuminate\Foundation\Http\FormRequest;

abstract class NormalizedEmailRequest extends FormRequest
{
    /**
     * Keep only the expected fields and normalize the address before any rule evaluates it.
     * Unexpected fields are dropped so that malformed values are never flashed to the session.
     */
    protected function prepareForValidation(): void
    {
        $input = $this->only($this->expectedFields());

        if (is_string($input['email'] ?? null)) {
            $input['email'] = EmailAddress::normalize($input['email']);
        }

        $this->replace($input);
    }

    /**
     * @return array<int, string>
     */
    protected function expectedFields(): array
    {
        return array_keys($this->rules());
    }

    /**
     * @return array<string, array<int, string>>
     */
    protected function emailRules(): array
    {
        return ['email' => ['bail', 'required', 'string', 'email', 'max:255']];
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
