<?php
declare(strict_types=1);

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

$root = rtrim($argv[1] ?? getcwd(), DIRECTORY_SEPARATOR);
$forbidden = [
    'src/Domain',
    'src/DomainInterface',
    'src/Adapter',
    'src/Infra',
    'src/InfraInterface',
];
$fail = 0;

foreach ($forbidden as $relative) {
    $path = $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relative);
    if (is_dir($path)) {
        fwrite(STDERR, "Forbidden canon path present: {$relative}
");
        ++$fail;
    }
}

exit($fail > 0 ? 1 : 0);
