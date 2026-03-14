<?php
declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 */
use App\Service\Import\Category\ImportFieldMapper;
require_once __DIR__.'/../../vendor/autoload.php';
$mapper = new ImportFieldMapper();
$data = [
  ['external_id' => 'a1', 'title' => 'Phones', 'lang' => 'en'],
  ['external_id' => 'a2', 'title' => 'TV', 'lang' => 'uk'],
];
$out = [];
foreach ($data as $row) {
  $out[] = $mapper->map($row);
}
file_put_contents('report/category-import-mapped.json', json_encode($out, JSON_PRETTY_PRINT));
