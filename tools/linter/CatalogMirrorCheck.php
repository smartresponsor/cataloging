<?php
declare(strict_types=1);

/**
 * CLI: php tools/linter/CatalogMirrorCheck.php <project-root>
 * Ensures known <Layer> <-> <LayerInterface> mirrors exist for Catalog subordinate Category units.
 */
$root = $argv[1] ?? getcwd();
$layers = ['Entity','Service','Repository','Policy','Event','ValueObject'];
$fail = 0;

foreach ($layers as $layer) {
    $dir = $root . "/src/{$layer}/Category";
    if (!is_dir($dir)) {
        continue;
    }

    $iter = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($iter as $file) {
        if (!$file instanceof SplFileInfo || !$file->isFile() || $file->getExtension() !== 'php') {
            continue;
        }

        $name = basename($file->getPathname(), '.php');
        $iface = $root . "/src/{$layer}Interface/Category/{$name}Interface.php";

        if (!file_exists($iface) && $layer !== 'Event') {
            fwrite(STDERR, "Mirror missing for {$layer} {$name}: {$iface}
");
            $fail++;
        }
    }
}

exit($fail > 0 ? 1 : 0);
