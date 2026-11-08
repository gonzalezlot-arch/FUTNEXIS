<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const string EMAIL_COLLATION = 'utf8mb4_uca1400_as_ci';

    private const string PREVIOUS_COLLATION = 'utf8mb4_unicode_ci';

    /**
     * Addresses differing by case match; addresses differing by accents do not (DEC-CORE-008, DEC-CORE-009).
     *
     * Both tables must share the collation: otherwise the reset token of one account would also match the other.
     */
    public function up(): void
    {
        $this->changeCollation(self::EMAIL_COLLATION);
    }

    /**
     * Reverse the migrations.
     *
     * Fails if addresses that only differ by accents exist, because the previous collation treats them as duplicates.
     */
    public function down(): void
    {
        $this->changeCollation(self::PREVIOUS_COLLATION);
    }

    private function changeCollation(string $collation): void
    {
        Schema::table('users', function (Blueprint $table) use ($collation) {
            $table->string('email')->collation($collation)->change();
        });

        Schema::table('password_reset_tokens', function (Blueprint $table) use ($collation) {
            $table->string('email')->collation($collation)->change();
        });
    }
};
