<?php
declare(strict_types=1);
$root = dirname(__DIR__, 2);
$checks = ['composer.json','src','config','tests'];
$missing = [];
foreach ($checks as $path) {
    if (!file_exists($root . '/' . $path)) { $missing[] = $path; }
}
echo json_encode(['missing_count'=>count($missing),'missing'=>$missing,'runtime_surface_ready'=>count($missing)===0], JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) . PHP_EOL;
exit(count($missing)===0 ? 0 : 1);
