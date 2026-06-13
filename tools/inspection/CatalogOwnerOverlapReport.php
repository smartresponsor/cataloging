<?php
declare(strict_types=1);

$root = dirname(__DIR__, 2);
$src = $root . '/src';
$reportDir = $root . '/report/inspection';
@mkdir($reportDir, 0777, true);
$out = $reportDir . '/catalog-owner-overlap-report.json';

$overlaps = [];
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($src, FilesystemIterator::SKIP_DOTS));
foreach ($iterator as $file) {
    if (!$file->isFile() || $file->getExtension() !== 'php') {
        continue;
    }
    $relative = str_replace($root . DIRECTORY_SEPARATOR, '', $file->getPathname());
    $nameEntity = $file->getBasename('.php');
    $signals = [];
    if (str_contains($nameEntity, 'Catalog') && str_contains($relative, '/CategoryEntity/')) {
        $signals[] = 'catalog-nameEntity-inside-category-scope';
    }
    if (str_contains($nameEntity, 'CategoryEntity') && str_contains($relative, '/Catalog')) {
        $signals[] = 'category-nameEntity-inside-catalog-scope';
    }
    if ($signals !== []) {
        $overlaps[] = ['file' => $relative, 'class' => $nameEntity, 'signals' => $signals];
    }
}

file_put_contents($out, json_encode(['generatedAt' => date(DATE_ATOM), 'count' => count($overlaps), 'items' => $overlaps], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo sprintf("[CatalogOwnerOverlapReport] %d overlap signals written to %s\n", count($overlaps), str_replace($root . DIRECTORY_SEPARATOR, '', $out));
exit(0);
