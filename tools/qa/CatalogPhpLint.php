<?php
declare(strict_types=1);

$root = dirname(__DIR__, 2);
$scan = ['src', 'tests', 'config'];
$php = PHP_BINARY;
$errors = [];

foreach ($scan as $dir) {
    $full = $root . DIRECTORY_SEPARATOR . $dir;
    if (!is_dir($full)) {
        continue;
    }
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($full));
    foreach ($it as $file) {
        if (!$file instanceof SplFileInfo || !$file->isFile() || $file->getExtension() !== 'php') {
            continue;
        }
        $cmd = escapeshellarg($php) . ' -l ' . escapeshellarg($file->getPathname());
        exec($cmd, $out, $code);
        if ($code !== 0) {
            $errors[] = [
                'file' => str_replace($root . DIRECTORY_SEPARATOR, '', $file->getPathname()),
                'output' => implode("\n", $out),
            ];
        }
    }
}

echo json_encode([
    'checked_roots' => $scan,
    'error_count' => count($errors),
    'errors' => $errors,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;

exit(count($errors) === 0 ? 0 : 1);
