<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

/**
 * Provides the import pipeline application service.
 */
final class ImportPipeline
{
    private string $dlqPath;

    /**
     * Initializes the import pipeline service collaborators.
     */
    public function __construct(string $dlqPath)
    {
        $this->dlqPath = $dlqPath;
    }

    /** @param array<string, mixed> $item */
    public function process(array $item): bool
    {
        return 'ok' === $this->processResult($item)['status'];
    }

    /**
     * @param array<string, mixed> $item
     *
     * @return array{status:'ok'|'failed', key:string, reason:?string}
     */
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

    /** @param array<string, mixed> $item */
    private function toDlq(array $item, string $reason): void
    {
        if (!is_dir($this->dlqPath)) {
            throw new \RuntimeException('DLQ path does not exist: '.$this->dlqPath);
        }

        if (!is_writable($this->dlqPath)) {
            throw new \RuntimeException('DLQ path is not writable: '.$this->dlqPath);
        }

        $line = json_encode(
            ['ts' => time(), 'reason' => $reason, 'item' => $item],
            JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES,
        );
        $target = $this->dlqPath.'/dlq.ndjson';
        $written = @file_put_contents($target, $line."\n", FILE_APPEND);
        if (false === $written) {
            throw new \RuntimeException('Failed to append to DLQ file: '.$target);
        }
    }

    /** @param array<string, mixed> $item */
    private function key(array $item): string
    {
        $encoded = json_encode([
            $this->scalarString($item['slug'] ?? ''),
            $this->scalarString($item['locale'] ?? 'en'),
        ], JSON_UNESCAPED_SLASHES);
        if (false === $encoded) {
            throw new \RuntimeException('Failed to encode import item key');
        }

        return sha1($encoded);
    }

    /** @param array<string, mixed> $item */
    private function assertProcessable(array $item): void
    {
        $slug = $item['slug'] ?? null;
        if (!is_string($slug) || '' === trim($slug)) {
            throw new \InvalidArgumentException('Import item slug is required');
        }
    }

    private function scalarString(mixed $value, string $default = ''): string
    {
        return is_scalar($value) ? trim((string) $value) : $default;
    }
}
