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
            'token' => ['bail', 'required', 'string', 'alpha_num:ascii', 'max:255'],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
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
            'token.string' => 'El enlace de restablecimiento no es válido.',
            'token.alpha_num' => 'El enlace de restablecimiento no es válido.',
            'token.max' => 'El enlace de restablecimiento no es válido.',
        ];
    }

    /**
     * @return array<int, string>
     */
    protected function expectedFields(): array
    {
        return ['email', 'token', 'password', 'password_confirmation'];
    }

    /**
     * Return to the form explicitly: its address carries a token and is not kept in the session.
     */
    protected function getRedirectUrl(): string
    {
        $token = $this->input('token');

        if (! is_string($token) || preg_match('/\A[A-Za-z0-9]+\z/', $token) !== 1) {
            return route('password.request');
        }

        return route('password.reset', ['token' => $token]);
    }
}
