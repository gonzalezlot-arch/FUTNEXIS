<?php

namespace App\Modules\Core\Http\Requests;

class EmailOnlyRequest extends NormalizedEmailRequest
{
    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return $this->emailRules();
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return $this->emailMessages();
    }
}
