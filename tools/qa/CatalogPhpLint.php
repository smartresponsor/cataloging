<?php
declare(strict_types=1);

$root = dirname(__DIR__, 2);
$targets = ['src', 'tests', 'tools'];
$php = PHP_BINARY;
$bad = [];

foreach ($targets as $target) {
    $dir = $root . DIRECTORY_SEPARATOR . $target;
    if (!is_dir($dir)) {
        continue;
    }

    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS));
    foreach ($it as $file) {
        if (!$file instanceof SplFileInfo || $file->getExtension() !== 'php') {
            continue;
        }

        $cmd = escapeshellarg($php) . ' -l ' . escapeshellarg($file->getPathname()) . ' 2>&1';
        exec($cmd, $output, $code);

        if ($code !== 0) {
            $bad[] = [
                'path' => str_replace($root . DIRECTORY_SEPARATOR, '', $file->getPathname()),
                'output' => implode("\n", $output),
            ];
        }
    }
}

echo json_encode([
    'checked_roots' => $targets,
    'bad_count' => count($bad),
    'bad' => $bad,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;

exit(count($bad) === 0 ? 0 : 1);
