<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

final class Importer
{
    public function importCsv(string $path): int
    {
        $count = 0;
        $fh = fopen($path, 'r');
        if (false === $fh) {
            throw new \RuntimeException('Cannot open CSV');
        }
        $header = null;
        while (($row = fgetcsv($fh)) !== false) {
            if (null === $header) {
                $header = $row;
                continue;
            }
            $item = array_combine($header, $row);
            $this->upsert($item);
            ++$count;
        }
        fclose($fh);

        return $count;
    }

    public function importJson(string $path): int
    {
        $raw = file_get_contents($path);
        if (false === $raw) {
            throw new \RuntimeException('Cannot read JSON');
        }
        $list = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        $count = 0;
        foreach ($list as $item) {
            $this->upsert($item);
            ++$count;
        }

        return $count;
    }

    private function upsert(array $item): void
    {
        // Application layer should map and persist.
        // Invariant checks occur before write.
        if (empty($item['slug'] ?? '')) {
            throw new \InvalidArgumentException('Slug is required');
        }
    }
}
