<?php
declare(strict_types=1);
$root = dirname(__DIR__, 2);
$fixtures = [];
if (is_dir($root . '/fixtures/Category')) {
    foreach (new FilesystemIterator($root . '/fixtures/Category') as $file) {
        if ($file->isFile()) { $fixtures[] = $file->getFilename(); }
    }
}
echo json_encode(['fixture_count'=>count($fixtures),'fixtures'=>$fixtures], JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) . PHP_EOL;
exit(count($fixtures)>0 ? 0 : 1);
