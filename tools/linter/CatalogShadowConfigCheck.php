<?php
declare(strict_types=1);

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

$root = $argv[1] ?? getcwd();
$root = is_string($root) ? $root : getcwd();
$shadowRoot = $root . '/config/config';
$fail = 0;

if (is_dir($shadowRoot)) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($shadowRoot));
    foreach ($iterator as $file) {
        if (!$file->isFile()) {
            continue;
        }

        $shadowPath = str_replace('\\', '/', $file->getPathname());
        $normalizedShadowRoot = str_replace('\\', '/', $shadowRoot);
        $relative = substr($shadowPath, strlen($normalizedShadowRoot) + 1);
        $canonicalPath = str_replace('\\', '/', $root . '/config/' . $relative);
        if (file_exists($canonicalPath)) {
            fwrite(STDERR, 'Shadow config duplicate: config/config/' . $relative . ' -> config/' . $relative . PHP_EOL);
            $fail++;
        }
    }
}

exit($fail > 0 ? 1 : 0);
