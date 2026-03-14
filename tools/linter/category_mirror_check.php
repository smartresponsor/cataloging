<?php
declare(strict_types=1);

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

$root = $argv[1] ?? getcwd();
$root = is_string($root) ? $root : getcwd();
$layers = ['Entity', 'Service', 'Repository', 'Policy', 'Event', 'ValueObject'];
$fail = 0;

foreach ($layers as $layer) {
    $domainDir = $root . '/src/' . $layer . '/Category';
    if (!is_dir($domainDir)) {
        continue;
    }

    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($domainDir));
    foreach ($iterator as $file) {
        if (!$file->isFile() || substr($file->getFilename(), -4) !== '.php') {
            continue;
        }

        $name = basename($file->getPathname(), '.php');
        $interfacePath = $root . '/src/' . $layer . 'Interface/Category/' . $name . 'Interface.php';
        if ($layer !== 'Event' && !file_exists($interfacePath)) {
            fwrite(STDERR, 'Mirror missing for ' . $layer . ' ' . $name . ': ' . $interfacePath . PHP_EOL);
            $fail++;
        }
    }
}

exit($fail > 0 ? 1 : 0);
