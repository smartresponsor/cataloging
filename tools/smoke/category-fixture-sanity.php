<?php
declare(strict_types=1);

$root = dirname(__DIR__, 2);
$fixtureDir = $root . '/src/DataFixtures';
$fixtures = [];

if (is_dir($fixtureDir)) {
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($fixtureDir, FilesystemIterator::SKIP_DOTS));
    foreach ($it as $file) {
        if ($file instanceof SplFileInfo && $file->getExtension() === 'php') {
            $fixtures[] = str_replace($root . DIRECTORY_SEPARATOR, '', $file->getPathname());
        }
    }
}

echo json_encode([
    'fixture_count' => count($fixtures),
    'fixtures' => $fixtures,
    'fixture_surface_ready' => count($fixtures) > 0,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;

exit(count($fixtures) > 0 ? 0 : 1);
