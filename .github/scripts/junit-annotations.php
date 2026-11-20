<?php

/**
 * Publishes each failed or errored test of a JUnit report as a GitHub Actions error annotation,
 * so failures are visible in the pull request without opening the job log.
 *
 * Usage: php .github/scripts/junit-annotations.php <junit.xml>
 */
$report = $argv[1] ?? '';

if (! is_file($report)) {
    echo "::error title=Pruebas::No se generó el informe JUnit ({$report}); la suite no llegó a ejecutarse.\n";
    exit(0);
}

$xml = simplexml_load_file($report);

if ($xml === false) {
    echo "::error title=Pruebas::El informe JUnit no es válido.\n";
    exit(0);
}

$escape = static fn (string $value): string => str_replace(['%', "\r", "\n"], ['%25', '%0D', '%0A'], $value);
$published = 0;

foreach ($xml->xpath('//testcase[failure or error]') as $testCase) {
    $problem = $testCase->failure ?? $testCase->error;
    $title = trim((string) $testCase['class']).'::'.trim((string) $testCase['name']);
    $message = trim((string) $problem['message']) !== '' ? (string) $problem['message'] : (string) $problem;
    $file = (string) $testCase['file'];
    $location = $file !== '' ? 'file='.$escape(ltrim(str_replace(getcwd(), '', $file), '/\\')).',line='.(int) $testCase['line'].',' : '';

    echo '::error '.$location.'title='.$escape($title).'::'.$escape(mb_substr(trim($message), 0, 1500))."\n";
    $published++;
}

echo "Pruebas con fallo publicadas como anotación: {$published}\n";
