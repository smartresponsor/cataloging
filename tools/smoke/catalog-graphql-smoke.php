<?php
declare(strict_types=1);
$root = dirname(__DIR__, 2);
$count = 0;
if (is_dir($root . '/src/GraphQl')) {
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root . '/src/GraphQl'));
    foreach ($it as $file) {
        if ($file instanceof SplFileInfo && $file->isFile() && $file->getExtension()==='php') { $count++; }
    }
}
echo json_encode(['graphql_php_count'=>$count,'graphql_surface_ready'=>$count>0], JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) . PHP_EOL;
exit($count>0 ? 0 : 1);
