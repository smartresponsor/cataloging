<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

$root = dirname(__DIR__, 2);
$php = escapeshellarg(PHP_BINARY);
$runner = escapeshellarg($root.'/tools/php/php84.php');
$phpunit = escapeshellarg($root.'/vendor/bin/phpunit');
$config = escapeshellarg($root.'/phpunit.xml.dist');
$suitePath = escapeshellarg($root.'/tests/CategoryEntity/Api');

/**
 * Run API test surface against local/docker Postgres DSNs when provided:
 * - CATEGORY_TEST_LOCAL_DATABASE_URL
 * - CATEGORY_TEST_DOCKER_DATABASE_URL
 */
$targets = [
    'local-postgres' => getenv('CATEGORY_TEST_LOCAL_DATABASE_URL') ?: '',
    'docker-postgres' => getenv('CATEGORY_TEST_DOCKER_DATABASE_URL') ?: '',
];

$executed = 0;
$failed = 0;

/**
 * @param array<string, string> $extraEnv
 */
function runCommand(string $command, array $extraEnv = []): int
{
    $descriptors = [
        0 => STDIN,
        1 => STDOUT,
        2 => STDERR,
    ];

    $serverEnvironment = array_filter(
        $_SERVER,
        static fn (mixed $value): bool => is_scalar($value),
    );
    $serverEnvironment = array_map(static fn (mixed $value): string => (string) $value, $serverEnvironment);
    $environment = array_merge($_ENV, $serverEnvironment, $extraEnv);
    $process = proc_open($command, $descriptors, $pipes, null, $environment);

    if (!is_resource($process)) {
        fwrite(STDERR, sprintf("[category-postgres-matrix-smoke] Unable to start command: %s\n", $command));

        return 1;
    }

    return proc_close($process);
}

/**
 * Returns true when the DSN host and port are reachable quickly enough for a DB-backed smoke run.
 */
function databaseEndpointReachable(string $dsn, float $timeoutSeconds = 1.0): bool
{
    $parts = parse_url($dsn);
    if (!is_array($parts)) {
        return false;
    }

    $host = $parts['host'] ?? null;
    if (!is_string($host) || trim($host) === '') {
        return false;
    }

    $scheme = is_string($parts['scheme'] ?? null) ? strtolower($parts['scheme']) : '';
    $defaultPort = match ($scheme) {
        'postgres', 'postgresql', 'pgsql' => 5432,
        default => null,
    };

    $port = $parts['port'] ?? $defaultPort;
    if (!is_int($port) || $port < 1) {
        return false;
    }

    $errno = 0;
    $errstr = '';
    $socket = @fsockopen($host, $port, $errno, $errstr, $timeoutSeconds);
    if (!is_resource($socket)) {
        return false;
    }

    fclose($socket);

    return true;
}

foreach ($targets as $target => $dsn) {
    if (!is_string($dsn) || trim($dsn) === '') {
        fwrite(STDOUT, sprintf("[category-postgres-matrix-smoke] SKIP %s: DSN env var is not set.\n", $target));
        continue;
    }

    if (!databaseEndpointReachable($dsn)) {
        fwrite(STDOUT, sprintf("[category-postgres-matrix-smoke] SKIP %s: database endpoint is not reachable within the smoke timeout.\n", $target));
        continue;
    }

    $command = sprintf(
        'DATABASE_URL=%s %s %s %s -c %s %s',
        escapeshellarg($dsn),
        $php,
        $runner,
        $phpunit,
        $config,
        $suitePath
    );

    fwrite(STDOUT, sprintf("[category-postgres-matrix-smoke] RUN %s\n", $target));
    $exitCode = runCommand($command, ['DATABASE_URL' => $dsn]);
    $executed++;

    if ($exitCode !== 0) {
        fwrite(STDERR, sprintf("[category-postgres-matrix-smoke] FAIL %s (exit=%d)\n", $target, $exitCode));
        $failed++;
    } else {
        fwrite(STDOUT, sprintf("[category-postgres-matrix-smoke] PASS %s\n", $target));
    }
}

if ($executed === 0) {
    fwrite(STDOUT, "[category-postgres-matrix-smoke] No DSNs provided. Nothing executed.\n");
    exit(0);
}

exit($failed > 0 ? 1 : 0);
