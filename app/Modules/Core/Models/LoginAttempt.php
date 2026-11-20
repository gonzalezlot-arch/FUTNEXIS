<?php

namespace App\Modules\Core\Models;

use App\Modules\Core\Enums\LoginOutcome;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'ip_address', 'user_agent', 'outcome', 'attempted_at'])]
class LoginAttempt extends Model
{
    /**
     * Attempts are immutable records and only carry their own timestamp.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, mixed>
     */
    protected function casts(): array
    {
        return [
            'outcome' => LoginOutcome::class,
            'attempted_at' => 'datetime',
        ];
    }
}
