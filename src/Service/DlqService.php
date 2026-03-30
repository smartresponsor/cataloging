<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

final class DlqService
{
    private string $file = 'report/category-dlq.json';

    /** @return list<array<string, mixed>> */
    public function all(): array
    {
        if (!is_file($this->file)) {
            return [];
        }

        $raw = file_get_contents($this->file);
        if (false === $raw) {
            return [];
        }

        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            return [];
        }

        /* @var list<array<string, mixed>> $decoded */
        return $decoded;
    }

    public function requeue(string $id): void
    {
        $all = $this->all();
        $all = array_values(array_filter(
            $all,
            static fn (array $x): bool => ($x['id'] ?? '') !== $id
        ));
        file_put_contents($this->file, json_encode($all, JSON_PRETTY_PRINT));
        file_put_contents('report/category-dlq-requeue.log', $id."\n", flags: FILE_APPEND);
    }
}
