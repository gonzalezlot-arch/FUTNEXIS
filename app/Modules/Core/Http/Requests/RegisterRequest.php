<?php

namespace App\Modules\Core\Http\Requests;

use App\Modules\Core\Actions\RegisterUser;
use App\Modules\Core\Http\Requests\Concerns\HasPasswordPolicyMessages;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends NormalizedEmailRequest
{
    use HasPasswordPolicyMessages;

    /**
     * @return array<string, array<int, ValidationRule|string>>
     */
    public function rules(): array
    {
        return [
            'email' => [...$this->emailRules()['email'], Rule::unique('users', 'email')],
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
            'email.unique' => RegisterUser::DUPLICATE_EMAIL_MESSAGE,
        ];
    }
}
