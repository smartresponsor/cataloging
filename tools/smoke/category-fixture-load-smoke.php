<?php
declare(strict_types=1);

$root = dirname(__DIR__, 2);
$fixtureDir = $root . '/src/DataFixtures';
$fixtureCount = 0;

if (is_dir($fixtureDir)) {
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($fixtureDir, FilesystemIterator::SKIP_DOTS));
    foreach ($it as $file) {
        if ($file instanceof SplFileInfo && $file->getExtension() === 'php') {
            $fixtureCount++;
        }
    }
}

echo json_encode([
    'fixture_count' => $fixtureCount,
    'load_surface_ready' => $fixtureCount > 0,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;

exit($fixtureCount > 0 ? 0 : 1);
