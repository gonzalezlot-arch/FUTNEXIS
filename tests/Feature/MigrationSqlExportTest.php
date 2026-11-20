<?php

namespace Tests\Feature;

use Illuminate\Database\Migrations\Migrator;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MigrationSqlExportTest extends TestCase
{
    public function test_the_script_recreates_the_schema_and_laravel_recognises_every_migration(): void
    {
        $script = $this->export();

        Schema::dropAllTables();
        DB::unprepared($script);

        $migrator = app(Migrator::class);
        $expected = array_keys($migrator->getMigrationFiles([database_path('migrations')]));

        $this->assertSame($expected, $migrator->getRepository()->getRan());
        $this->assertTrue(Schema::hasTable('login_throttles'));
        $this->assertSame(0, Artisan::call('migrate', ['--pretend' => true, '--force' => true]));
        $this->assertStringContainsString('Nothing to migrate', Artisan::output());
    }

    public function test_the_script_includes_the_collation_preflight_and_the_migration_records(): void
    {
        $script = $this->export();

        $this->assertStringContainsString("SELECT 'a' COLLATE utf8mb4_uca1400_as_ci;", $script);
        $this->assertStringContainsString('CREATE TABLE IF NOT EXISTS `migrations`', $script);
        $this->assertMatchesRegularExpression("/INSERT INTO `migrations` .* VALUES \('0001_01_01_000000_create_users_table', 1\);/", $script);
        $this->assertStringContainsString("collate 'utf8mb4_uca1400_as_ci'", $script);
    }

    public function test_the_script_can_start_after_the_last_applied_migration(): void
    {
        $script = $this->export(['--after' => '2026_11_08_153555_create_login_attempts_table', '--batch' => 3]);

        $this->assertStringNotContainsString('create_users_table', $script);
        $this->assertStringContainsString("'2026_11_08_230549_make_password_nullable_on_users_table', 3", $script);
    }

    public function test_an_unknown_starting_point_is_rejected(): void
    {
        $this->assertSame(1, Artisan::call('migrations:export-sql', ['--after' => 'no_existe']));
    }

    /**
     * @param  array<string, mixed>  $options
     */
    private function export(array $options = []): string
    {
        $path = tempnam(sys_get_temp_dir(), 'futnexis-sql-');

        try {
            $this->assertSame(0, Artisan::call('migrations:export-sql', [...$options, '--output' => $path]));

            return (string) file_get_contents($path);
        } finally {
            unlink($path);
        }
    }
}
