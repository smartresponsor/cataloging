<?php
// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$bad = [];
$patterns = [
    'namespace App\\Service\\Category\\Service;',
    'namespace App\\Service\\Category\\Repository;',
    'namespace App\\Service\\Category\\Domain;',
    'namespace App\\ServiceInterface\\Category\\Domain;',
    'namespace App\\ServiceInterface\\Category\\Domaine;',
    'namespace App\\Service\\Category\\Domain\\',
    'namespace App\\ServiceInterface\\Category\\Domain\\',
    'namespace App\\ServiceInterface\\Category\\Domaine\\',
];
$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root . '/src'));
foreach ($it as $file) {
    if (!$file->isFile() || $file->getExtension() !== 'php') {
        continue;
    }
    $content = file_get_contents($file->getPathname()) ?: '';
    foreach ($patterns as $pattern) {
        if (str_contains($content, $pattern)) {
            $bad[] = [
                'file' => substr($file->getPathname(), strlen($root) + 1),
                'pattern' => $pattern,
            ];
            break;
        }
    }
}
if ($bad !== []) {
    foreach ($bad as $row) {
        fwrite(STDERR, $row['file'] . ' :: ' . $row['pattern'] . PHP_EOL);
    }
    exit(1);
}
echo "OK\n";
