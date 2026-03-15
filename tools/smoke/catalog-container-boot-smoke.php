<?php
declare(strict_types=1);
$root = dirname(__DIR__, 2);
$missing = [];
foreach (['config/services.yaml','src/Kernel.php'] as $path) {
    if (!file_exists($root . '/' . $path)) { $missing[] = $path; }
}
echo json_encode(['missing_count'=>count($missing),'missing'=>$missing,'kernel_imports_config'=>file_exists($root . '/config/services.yaml')], JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) . PHP_EOL;
exit(count($missing)===0 ? 0 : 1);
