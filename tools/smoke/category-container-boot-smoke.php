<?php
declare(strict_types=1);

$root = dirname(__DIR__, 2);
$checks = [
    'kernel' => $root . '/src/Kernel.php',
    'bundles-config' => $root . '/config/catalog_bundles.php',
    'api-doc-config' => $root . '/config/packages/nelmio_api_doc.yaml',
    'api-doc-routes' => $root . '/config/routes/nelmio_api_doc.yaml',
    'canonical-openapi' => $root . '/api/catalog-openapi.yaml',
    'package-dir' => $root . '/config/packages',
];
$missing = [];
foreach ($checks as $label => $path) {
    $ok = str_ends_with($label, '-dir') ? is_dir($path) : file_exists($path);
    if (!$ok) {
        $missing[$label] = $path;
    }
}

if ($missing !== []) {
    foreach ($missing as $label => $path) {
        fwrite(STDERR, sprintf("[category-container-boot-smoke] Missing %s: %s\n", $label, $path));
    }
    exit(1);
}

echo "[category-container-boot-smoke] Container boot prerequisites present.\n";
exit(0);
