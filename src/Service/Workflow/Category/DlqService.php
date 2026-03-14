<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Service\Workflow\Category;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class DlqService
{
    private string $file = 'report/catalog-dlq.json';
    private LoggerInterface $logger;

    public function __construct(?LoggerInterface $logger = null)
    {
        $this->logger = $logger ?? new NullLogger();
    }

    public function all(): array
    {
        if (!is_file($this->file)) {
            return [];
        }

        $raw = file_get_contents($this->file);
        if (false === $raw) {
            $this->logger->error('The category DLQ file could not be read.', ['path' => $this->file]);

            return [];
        }

        try {
            $decoded = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            $this->logger->error('The category DLQ file is invalid JSON.', [
                'path' => $this->file,
                'exception' => $exception,
            ]);

            return [];
        }

        return is_array($decoded) ? $decoded : [];
    }

    public function requeue(string $id): void
    {
        if ('' === $id) {
            throw new \RuntimeException('The DLQ identifier must not be empty.');
        }

        $all = $this->all();
        $all = array_values(array_filter($all, static fn ($x) => ($x['id'] ?? '') !== $id));

        $payload = json_encode($all, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
        if (false === file_put_contents($this->file, $payload)) {
            throw new \RuntimeException('The category DLQ file could not be updated.');
        }

        if (false === file_put_contents('report/category-dlq-requeue.log', $id."\n", FILE_APPEND | LOCK_EX)) {
            throw new \RuntimeException('The category DLQ requeue log could not be written.');
        }
    }
}
