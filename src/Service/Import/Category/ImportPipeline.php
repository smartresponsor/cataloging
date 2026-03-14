<?php

declare(strict_types=1);
/*
Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
Author: Oleksandr Tishchenko <dev@smartresponsor.com>
Owner: Marketing America Corp
*/

namespace App\Service\Import\Category;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class ImportPipeline
{
    private LoggerInterface $logger;

    public function __construct(
        private readonly string $dlqPath,
        ?LoggerInterface $logger = null,
    ) {
        $this->logger = $logger ?? new NullLogger();
    }

    public function process(array $item): bool
    {
        $this->key($item);

        try {
            return true;
        } catch (\Throwable $throwable) {
            $reason = $this->humanMessage($throwable, 'The item could not be imported.');
            $this->toDlq($item, $reason, $throwable->getMessage());

            $this->logger->error('Category import pipeline item failed.', [
                'item' => $item,
                'reason' => $reason,
                'exception' => $throwable,
            ]);

            return false;
        }
    }

    private function toDlq(array $item, string $reason, string $detail): void
    {
        if (!is_dir($this->dlqPath)) {
            mkdir($this->dlqPath, 0775, true);
        }

        $line = json_encode([
            'ts' => time(),
            'reason' => $reason,
            'detail' => $detail,
            'item' => $item,
        ], JSON_UNESCAPED_SLASHES);

        file_put_contents($this->dlqPath.'/dlq.ndjson', $line."\n", FILE_APPEND);
    }

    private function key(array $item): string
    {
        return sha1(json_encode([$item['slug'] ?? '', $item['locale'] ?? 'en'], JSON_UNESCAPED_SLASHES));
    }

    private function humanMessage(\Throwable $throwable, string $fallback): string
    {
        $message = trim($throwable->getMessage());

        if ('' === $message) {
            return $fallback;
        }

        return rtrim(ucfirst($message), '.').'.';
    }
}
