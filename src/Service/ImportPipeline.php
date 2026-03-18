<?php

declare(strict_types=1);
/*
Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
Author: Oleksandr Tishchenko <dev@smartresponsor.com>
Owner: Marketing America Corp
*/

namespace App\Service;

final class ImportPipeline
{
    private string $dlqPath;

    public function __construct(string $dlqPath)
    {
        $this->dlqPath = $dlqPath;
    }

    public function process(array $item): bool
    {
        return 'ok' === $this->processResult($item)['status'];
    }

    /** @return array{status:'ok'|'failed', key:string, reason:?string} */
    public function processResult(array $item): array
    {
        $key = $this->key($item);

        try {
            $this->assertProcessable($item);

            return ['status' => 'ok', 'key' => $key, 'reason' => null];
        } catch (\RuntimeException|\InvalidArgumentException|\TypeError $e) {
            $this->toDlq($item, $e->getMessage());

            return ['status' => 'failed', 'key' => $key, 'reason' => $e->getMessage()];
        }
    }

    private function toDlq(array $item, string $reason): void
    {
        $line = json_encode(['ts' => time(), 'reason' => $reason, 'item' => $item], JSON_UNESCAPED_SLASHES);
        file_put_contents($this->dlqPath.'/dlq.ndjson', $line.'
', FILE_APPEND);
    }

    private function key(array $item): string
    {
        return sha1(json_encode([$item['slug'] ?? '', $item['locale'] ?? 'en'], JSON_UNESCAPED_SLASHES));
    }

    private function assertProcessable(array $item): void
    {
        $slug = $item['slug'] ?? null;
        if (!is_string($slug) || '' === trim($slug)) {
            throw new \InvalidArgumentException('Import item slug is required');
        }
    }
}
