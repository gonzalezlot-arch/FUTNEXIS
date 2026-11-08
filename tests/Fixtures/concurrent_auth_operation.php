<?php

/**
 * Runs one authentication operation in its own process, for the concurrency tests.
 *
 * Usage: php concurrent_auth_operation.php <login|reset> <startEpoch> <email> <secret>
 */

use App\Modules\Core\Actions\AuthenticateUser;
use App\Modules\Core\Actions\ResetUserPassword;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Validation\ValidationException;

require __DIR__.'/../../vendor/autoload.php';

$app = require __DIR__.'/../../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$database = (string) $app->make('db')->connection()->getDatabaseName();

if (! str_ends_with($database, '_testing')) {
    fwrite(STDERR, "Refusing to run against {$database}\n");
    exit(2);
}

[, $operation, $startEpoch, $email, $secret] = $argv;

time_sleep_until((float) $startEpoch);

try {
    if ($operation === 'login') {
        $app->make(AuthenticateUser::class)->handle($email, $secret, '198.51.100.7', 'concurrency-test');
        echo "authenticated\n";
    } else {
        $accepted = $app->make(ResetUserPassword::class)->handle(
            ['email' => $email, 'token' => $secret, 'password' => 'Nueva#'.getmypid().'Aa1'],
            '198.51.100.7',
            'concurrency-test',
        );
        echo $accepted ? "reset\n" : "rejected\n";
    }
} catch (ValidationException) {
    echo "rejected\n";
} catch (Throwable $exception) {
    echo 'error '.$exception::class.' '.$exception->getMessage()."\n";
    exit(1);
}
