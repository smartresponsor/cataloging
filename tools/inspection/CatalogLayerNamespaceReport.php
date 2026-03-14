<?php
declare(strict_types=1);

$root = dirname(__DIR__, 2);
$patterns = [
    'namespace App\\Layer' => 'layer_namespace',
    'namespace App\\LayerInterface' => 'layer_interface_namespace',
    'namespace App\\Http\\' => 'http_namespace',
    'namespace App\\HttpInterface\\' => 'http_interface_namespace',
    'namespace App\\Domain\\' => 'domain_namespace',
    'namespace App\\Infra\\' => 'infra_namespace',
    'namespace App\\InfraInterface\\' => 'infra_interface_namespace',
    'namespace App\\Adapter\\' => 'adapter_namespace',
];
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root . '/src', FilesystemIterator::SKIP_DOTS));
foreach ($iterator as $file) {
    if (!$file->isFile() || $file->getExtension() !== 'php') {
        continue;
    }
    $relativePath = substr($file->getPathname(), strlen($root) + 1);
    $code = file_get_contents($file->getPathname());
    foreach ($patterns as $needle => $kind) {
        if (str_contains($code, $needle)) {
            echo json_encode([
                'path' => str_replace('\\', '/', $relativePath),
                'kind' => $kind,
            ], JSON_UNESCAPED_SLASHES) . PHP_EOL;
            break;
        }
    }
}
