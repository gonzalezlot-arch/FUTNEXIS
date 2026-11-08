<?php

namespace App\Modules\Core\Http\Requests;

use App\Modules\Core\Actions\RegisterUser;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

class RegisterRequest extends NormalizedEmailRequest
{
    /**
     * Only verified accounts count as registered: a pending address can be requested again by its real owner.
     *
     * @return array<string, array<int, ValidationRule|string>>
     */
    public function rules(): array
    {
        return [
            'email' => [...$this->emailRules()['email'], Rule::unique('users', 'email')->whereNotNull('email_verified_at')],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            ...$this->emailMessages(),
            'email.unique' => RegisterUser::DUPLICATE_EMAIL_MESSAGE,
        ];
    }
}
