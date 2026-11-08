<?php

namespace App\Modules\Core\Enums;

enum LoginOutcome: string
{
    case Succeeded = 'succeeded';
    case InvalidCredentials = 'invalid_credentials';
    case Unverified = 'unverified';
    case Locked = 'locked';
    case PasswordReset = 'password_reset';
}
