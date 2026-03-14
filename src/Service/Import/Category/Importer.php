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

final class Importer
{
    private LoggerInterface $logger;

    public function __construct(?LoggerInterface $logger = null)
    {
        $this->logger = $logger ?? new NullLogger();
    }

    public function importCsv(string $path): int
    {
        $handle = fopen($path, 'r');
        if (false === $handle) {
            throw new \RuntimeException('The CSV file could not be opened.');
        }

        try {
            $count = 0;
            $header = null;

            while (($row = fgetcsv($handle)) !== false) {
                if (null === $header) {
                    $header = $row;
                    continue;
                }

                $item = array_combine($header, $row);
                if (!is_array($item)) {
                    throw new \RuntimeException('The CSV row does not match the header columns.');
                }

                $this->upsert($item);
                ++$count;
            }

            return $count;
        } catch (\Throwable $throwable) {
            $this->logger->error('Category CSV importer failed.', [
                'path' => $path,
                'exception' => $throwable,
            ]);

            throw new \RuntimeException('The CSV importer could not complete the import. Check the logs if the problem continues.', 0, $throwable);
        } finally {
            fclose($handle);
        }
    }

    public function importJson(string $path): int
    {
        $raw = file_get_contents($path);
        if (false === $raw) {
            throw new \RuntimeException('The JSON file could not be read.');
        }

        try {
            $list = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
            if (!is_array($list)) {
                throw new \RuntimeException('The JSON file must contain an array of import items.');
            }

            $count = 0;
            foreach ($list as $item) {
                if (!is_array($item)) {
                    throw new \RuntimeException('Each JSON import item must be an object.');
                }

                $this->upsert($item);
                ++$count;
            }

            return $count;
        } catch (\Throwable $throwable) {
            $this->logger->error('Category JSON importer failed.', [
                'path' => $path,
                'exception' => $throwable,
            ]);

            throw new \RuntimeException('The JSON importer could not complete the import. Check the logs if the problem continues.', 0, $throwable);
        }
    }

    private function upsert(array $item): void
    {
        if (empty($item['slug'] ?? '')) {
            throw new \InvalidArgumentException('The import item slug is required.');
        }
    }
}
