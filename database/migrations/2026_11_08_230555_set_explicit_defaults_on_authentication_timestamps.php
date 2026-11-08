<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * With explicit_defaults_for_timestamp off, the first NOT NULL timestamp would silently update itself on every write.
     */
    public function up(): void
    {
        Schema::table('email_verification_tokens', function (Blueprint $table) {
            $table->timestamp('expires_at')->useCurrent()->change();
        });

        Schema::table('login_attempts', function (Blueprint $table) {
            $table->timestamp('attempted_at')->useCurrent()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('email_verification_tokens', function (Blueprint $table) {
            $table->timestamp('expires_at')->change();
        });

        Schema::table('login_attempts', function (Blueprint $table) {
            $table->timestamp('attempted_at')->change();
        });
    }
};
