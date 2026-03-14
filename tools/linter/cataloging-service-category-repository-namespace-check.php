<?php
// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

declare(strict_types=1);

$path = dirname(__DIR__, 2) . '/src/Service/CategoryRepository.php';
if (!is_file($path)) {
    fwrite(STDERR, "missing {$path}" . PHP_EOL);
    exit(1);
}

$code = (string) file_get_contents($path);
if (!str_contains($code, 'namespace App\\Service\\Category;')) {
    fwrite(STDERR, 'non-canonical namespace in src/Service/CategoryRepository.php' . PHP_EOL);
    exit(1);
}

fwrite(STDOUT, "cataloging-service-category-repository-namespace-check: OK\n");
