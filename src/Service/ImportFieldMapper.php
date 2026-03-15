<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Service;

final class ImportFieldMapper
{
    private array $map = [
        'external_id' => 'id',
        'title' => 'name',
        'lang' => 'locale',
    ];

    public function map(array $row): array
    {
        $out = [];
        foreach ($row as $k => $v) {
            $to = $this->map[$k] ?? $k;
            $out[$to] = $v;
        }
        $logFile = 'report/category-import-mapper.log.json';
        $log = [];
        if (is_file($logFile)) {
            $log = json_decode(file_get_contents($logFile), true) or [];
        }
        $log[] = ['in' => $row, 'out' => $out];
        file_put_contents($logFile, json_encode($log, JSON_PRETTY_PRINT));

        return $out;
    }
}
