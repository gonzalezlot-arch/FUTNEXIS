<?php

namespace Tests;

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    /**
     * Create the application, refusing to continue unless the default connection targets a testing database.
     *
     * This runs before the database traits, so a refresh can never reach another database.
     */
    public function createApplication(): Application
    {
        $application = parent::createApplication();

        static::ensureDatabaseIsForTesting((string) $application->make('db')->connection()->getDatabaseName());

        return $application;
    }

    /**
     * Stop the test run when the database name does not end in "_testing".
     *
     * @throws RuntimeException
     */
    protected static function ensureDatabaseIsForTesting(string $databaseName): void
    {
        if (! str_ends_with($databaseName, '_testing')) {
            throw new RuntimeException("Tests can only run against a database ending in \"_testing\"; got \"{$databaseName}\".");
        }
    }
}
