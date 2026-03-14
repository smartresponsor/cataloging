<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

declare(strict_types=1);

namespace App\Service\Import\Category;

use App\ServiceInterface\Import\Category\ImportRepositoryInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class ImportService
{
    private LoggerInterface $logger;

    public function __construct(
        private readonly ImportRepositoryInterface $repo,
        ?LoggerInterface $logger = null,
    ) {
        $this->logger = $logger ?? new NullLogger();
    }

    public function importCsv(string $file): int
    {
        $handle = fopen($file, 'r');
        if (false === $handle) {
            throw new \RuntimeException('The CSV file could not be opened.');
        }

        try {
            $header = fgetcsv($handle);
            if (!is_array($header) || [] === $header) {
                throw new \RuntimeException('The CSV header is required.');
            }

            $index = array_flip($header);
            $count = 0;

            while (($row = fgetcsv($handle)) !== false) {
                $payload = [
                    'id' => $row[$index['id']] ?? null,
                    'name' => $row[$index['name']] ?? null,
                    'slug' => $row[$index['slug']] ?? null,
                    'parentId' => $row[$index['parent_id']] ?? null,
                    'path' => $row[$index['path']] ?? null,
                    'level' => isset($index['level']) ? (int) ($row[$index['level']] ?? 0) : null,
                ];

                $this->validateCategory($payload);
                $this->repo->upsertCategory($payload);
                ++$count;
            }

            return $count;
        } catch (\Throwable $throwable) {
            $this->logger->error('Category CSV import failed.', [
                'file' => $file,
                'exception' => $throwable,
            ]);

            throw new \RuntimeException('The CSV import could not be completed. Check the logs if the problem continues.', 0, $throwable);
        } finally {
            fclose($handle);
        }
    }

    public function importNdjson(string $file): int
    {
        $handle = fopen($file, 'r');
        if (false === $handle) {
            throw new \RuntimeException('The NDJSON file could not be opened.');
        }

        try {
            $count = 0;
            $lineNumber = 0;

            while (($line = fgets($handle)) !== false) {
                ++$lineNumber;
                $payload = json_decode($line, true, 512, JSON_THROW_ON_ERROR);
                if (!is_array($payload)) {
                    throw new \RuntimeException(sprintf('The NDJSON row %d is invalid.', $lineNumber));
                }

                if (isset($payload['definition'])) {
                    $this->validateRule($payload);
                    $this->repo->upsertRule($payload);
                } else {
                    $this->validateCategory($payload);
                    $this->repo->upsertCategory($payload);
                }

                ++$count;
            }

            return $count;
        } catch (\Throwable $throwable) {
            $this->logger->error('Category NDJSON import failed.', [
                'file' => $file,
                'exception' => $throwable,
            ]);

            throw new \RuntimeException('The NDJSON import could not be completed. Check the logs if the problem continues.', 0, $throwable);
        } finally {
            fclose($handle);
        }
    }

    private function validateCategory(array $row): void
    {
        foreach (['id', 'name', 'slug'] as $field) {
            if (!isset($row[$field]) || !is_string($row[$field]) || '' === $row[$field]) {
                throw new \InvalidArgumentException(sprintf('Category field "%s" is required.', $field));
            }
        }
    }

    private function validateRule(array $row): void
    {
        foreach (['id', 'name', 'definition'] as $field) {
            if (!array_key_exists($field, $row)) {
                throw new \InvalidArgumentException(sprintf('Rule field "%s" is required.', $field));
            }
        }
    }
}
