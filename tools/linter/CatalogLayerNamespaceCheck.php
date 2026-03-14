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
$issues = [];
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root . '/src', FilesystemIterator::SKIP_DOTS));
foreach ($iterator as $file) {
    if (!$file->isFile() || $file->getExtension() !== 'php') {
        continue;
    }
    $relativePath = substr($file->getPathname(), strlen($root) + 1);
    $code = file_get_contents($file->getPathname());
    foreach ($patterns as $needle => $kind) {
        if (str_contains($code, $needle)) {
            $issues[] = ['path' => str_replace('\\', '/', $relativePath), 'kind' => $kind];
            break;
        }
    }
}
if ($issues === []) {
    fwrite(STDOUT, "OK: no forbidden namespace tokens found\n");
    exit(0);
}
foreach ($issues as $issue) {
    fwrite(STDERR, $issue['kind'] . ' ' . $issue['path'] . PHP_EOL);
}
exit(1);
