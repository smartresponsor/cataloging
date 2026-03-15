<?php
declare(strict_types=1);
$root = dirname(__DIR__, 2);
$entities = is_dir($root . '/src/Entity') ? iterator_count(new FilesystemIterator($root . '/src/Entity')) : 0;
$migrations = is_dir($root . '/migrations') ? iterator_count(new FilesystemIterator($root . '/migrations')) : 0;
echo json_encode(['entity_count'=>$entities,'migration_count'=>$migrations,'mapping_looks_ready'=>$entities>0], JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) . PHP_EOL;
exit($entities>0 ? 0 : 1);
