<?php
declare(strict_types=1);
// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

$root = dirname(__DIR__, 2);
$targets = [$root . '/src', $root . '/tests'];
$rows = [];

foreach ($targets as $target) {
    if (!is_dir($target)) {
        continue;
    }

    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($target));
    foreach ($iterator as $file) {
        if (!$file instanceof SplFileInfo || !$file->isFile() || $file->getExtension() !== 'php') {
            continue;
        }

        $path = $file->getPathname();
        $content = (string) file_get_contents($path);

        preg_match_all('/catch\s*\(([^\)]+)\)\s*\{([\s\S]*?)\}/m', $content, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $body = trim($match[2]);

            $rows[] = [
                'path' => str_replace($root . '/', '', $path),
                'exception' => trim($match[1]),
                'hasLoggerCall' => str_contains($body, 'logger->') || str_contains($body, 'logger =') || str_contains($body, '->error(') || str_contains($body, '->warning('),
                'hasThrow' => str_contains($body, 'throw '),
                'hasReturn' => str_contains($body, 'return '),
                'hasHumanMessage' => str_contains($body, 'message') || str_contains($body, 'error') || str_contains($body, 'report[]'),
                'isEmpty' => $body === '',
            ];
        }
    }
}

echo json_encode([
    'catch_count' => count($rows),
    'rows' => $rows,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
