<?php

namespace App\Modules\Core\Http\Requests;

use App\Modules\Core\Http\Requests\Concerns\HasPasswordPolicyMessages;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class SetInitialPasswordRequest extends FormRequest
{
    use HasPasswordPolicyMessages;

    /**
     * @return array<string, array<int, ValidationRule|string>>
     */
    public function rules(): array
    {
        return [
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return $this->passwordPolicyMessages();
    }

    /**
     * Unexpected fields are dropped so that malformed values are never flashed to the session.
     */
    protected function prepareForValidation(): void
    {
        $this->replace($this->only(['password', 'password_confirmation']));
    }

    /**
     * Return to the form explicitly: its address carries a token and is not kept in the session.
     */
    protected function getRedirectUrl(): string
    {
        return route('verification.show', ['token' => $this->route('token')]);
    }
}
