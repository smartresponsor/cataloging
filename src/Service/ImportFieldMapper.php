<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

/**
 * Provides the import field mapper application service.
 */
final class ImportFieldMapper
{
    /** @var array<string,string> */
    private array $map = [
        'external_id' => 'id',
        'title' => 'name',
        'lang' => 'locale',
    ];

    /**
     * @param array<string,mixed> $row
     *
     * @return array<string,mixed>
     *
     * @throws \JsonException
     */
    public function map(array $row): array
    {
        /** @var array<string,mixed> $out */
        $out = [];
        foreach ($row as $key => $value) {
            $target = $this->map[$key] ?? $key;
            $out[$target] = $value;
        }
        $logFile = 'report/category-import-mapper.log.json';
        $log = $this->readLog($logFile);
        $log[] = ['in' => $row, 'out' => $out];
        file_put_contents($logFile, json_encode($log, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));

        return $out;
    }

    /** @return list<array{in:array<string,mixed>,out:array<string,mixed>}> */
    private function readLog(string $path): array
    {
        if (!is_file($path)) {
            return [];
        }
        $json = file_get_contents($path);
        if (!is_string($json) || '' === $json) {
            return [];
        }
        $decoded = json_decode($json, true);
        if (!is_array($decoded)) {
            return [];
        }

        $out = [];
        foreach ($decoded as $row) {
            if (!is_array($row)) {
                continue;
            }
            $in = is_array($row['in'] ?? null) ? $row['in'] : [];
            $mapped = is_array($row['out'] ?? null) ? $row['out'] : [];
            $out[] = ['in' => $in, 'out' => $mapped];
        }

        return $out;
    }
}
