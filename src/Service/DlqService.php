<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Service;

final class DlqService
{
    private string $file = 'report/category-dlq.json';

    public function all(): array
    {
        if (!is_file($this->file)) {
            return [];
        }

        return json_decode(file_get_contents($this->file), true) ?? [];
    }

    public function requeue(string $id): void
    {
        $all = $this->all();
        $all = array_values(array_filter($all, static fn ($x) => ($x['id'] ?? '') !== $id));
        file_put_contents($this->file, json_encode($all, JSON_PRETTY_PRINT));
        file_put_contents('report/category-dlq-requeue.log', $id."\n", flags: FILE_APPEND);
    }
}
