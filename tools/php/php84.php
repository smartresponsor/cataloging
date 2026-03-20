<?php

declare(strict_types=1);
/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>
 * Owner: Marketing America Corp
 */

$binary = match (PHP_OS_FAMILY) {
    'Windows' => 'C:\\PHP\\php-8.4.13-nts-Win32-vs17-x64\\php.exe',
    default => '/usr/bin/php8.4',
};

$args = $argv;
array_shift($args);

$command = escapeshellarg($binary);

foreach ($args as $arg) {
    $command .= ' ' . escapeshellarg($arg);
}

passthru($command, $exitCode);
exit($exitCode);