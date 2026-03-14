<?php
// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$needleMap = [
    'App\\Domain\\Category' => 'domain-wrapper-namespace',
    'App\\ServiceInterface\\Category\\Domain' => 'domain-interface-bridge-namespace',
    'App\\Infra\\Category' => 'infra-wrapper-namespace',
    'App\\InfraInterface\\Category' => 'infra-interface-bridge-namespace',
    'App\\Adapter\\Category' => 'adapter-wrapper-namespace',
];

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root . '/src', FilesystemIterator::SKIP_DOTS));
foreach ($iterator as $file) {
    if (!$file->isFile() || $file->getExtension() !== 'php') {
        continue;
    }

    $path = str_replace($root . '/', '', $file->getPathname());
    $content = (string) file_get_contents($file->getPathname());
    foreach ($needleMap as $needle => $label) {
        if (str_contains($content, $needle)) {
            fwrite(STDOUT, $label . "\t" . $needle . "\t" . $path . PHP_EOL);
        }
    }
}
