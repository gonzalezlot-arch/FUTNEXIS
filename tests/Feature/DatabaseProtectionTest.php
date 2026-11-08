<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\DataProvider;
use RuntimeException;
use Tests\TestCase;

class DatabaseProtectionTest extends TestCase
{
    public function test_the_suite_connects_to_the_mariadb_testing_database(): void
    {
        $server = DB::selectOne('SELECT DATABASE() AS database_name, @@collation_database AS collation_name, VERSION() AS version');

        $this->assertSame('mariadb', DB::connection()->getDriverName());
        $this->assertSame('futnexis_testing', $server->database_name);
        $this->assertSame('utf8mb4_unicode_ci', $server->collation_name);
        $this->assertStringContainsString('MariaDB', $server->version);
    }

    public function test_a_database_ending_in_testing_is_accepted(): void
    {
        static::ensureDatabaseIsForTesting('futnexis_testing');

        $this->addToAssertionCount(1);
    }

    #[DataProvider('unprotectedDatabaseNames')]
    public function test_a_database_not_ending_in_testing_is_rejected(string $databaseName): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Tests can only run against a database ending in "_testing"');

        static::ensureDatabaseIsForTesting($databaseName);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function unprotectedDatabaseNames(): array
    {
        return [
            'local development database' => ['futnexis'],
            'production database' => ['gltechso_futnexis'],
            'suffix not at the end' => ['futnexis_testing_backup'],
            'suffix without underscore' => ['futnexistesting'],
            'in-memory sqlite' => [':memory:'],
            'empty name' => [''],
        ];
    }
}
