<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

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
            error_log('[ImportPipeline] '.$e->getMessage());
            $reason = $e->getMessage();

            try {
                $this->toDlq($item, $reason);
            } catch (\RuntimeException $dlqException) {
                error_log('[ImportPipeline][DLQ] '.$dlqException->getMessage());
                $reason .= ' | DLQ write failed: '.$dlqException->getMessage();
            }

            return ['status' => 'failed', 'key' => $key, 'reason' => $reason];
        }
    }

    private function toDlq(array $item, string $reason): void
    {
        if (!is_dir($this->dlqPath)) {
            throw new \RuntimeException('DLQ path does not exist: '.$this->dlqPath);
        }

        if (!is_writable($this->dlqPath)) {
            throw new \RuntimeException('DLQ path is not writable: '.$this->dlqPath);
        }

        $line = json_encode(['ts' => time(), 'reason' => $reason, 'item' => $item], JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES);
        $target = $this->dlqPath.'/dlq.ndjson';
        $written = @file_put_contents($target, $line."\n", FILE_APPEND);
        if (false === $written) {
            throw new \RuntimeException('Failed to append to DLQ file: '.$target);
        }
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
