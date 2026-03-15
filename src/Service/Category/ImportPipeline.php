<?php

declare(strict_types=1);
/*
Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
Author: Oleksandr Tishchenko <dev@smartresponsor.com>
Owner: Marketing America Corp
*/

namespace App\Layer\Category;

final class ImportPipeline
{
    private string $dlqPath;

    public function __construct(string $dlqPath)
    {
        $this->dlqPath = $dlqPath;
    }

    public function process(array $item): bool
    {
        $key = $this->key($item);
        try {
            // Upsert (application layer)
            return true;
        } catch (\Throwable $e) {
            $this->toDlq($item, $e->getMessage());

            return false;
        }
    }

    private function toDlq(array $item, string $reason): void
    {
        $line = json_encode(['ts' => time(), 'reason' => $reason, 'item' => $item], JSON_UNESCAPED_SLASHES);
        file_put_contents($this->dlqPath.'/dlq.ndjson', $line."\n", FILE_APPEND);
    }

    private function key(array $item): string
    {
        return sha1(json_encode([$item['slug'] ?? '', $item['locale'] ?? 'en'], JSON_UNESCAPED_SLASHES));
    }
}
