<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

declare(strict_types=1);

namespace App\Service\Query\Category;

class ApproxTotalService
{
    public function __construct(private readonly string $file)
    {
    }

    public function get(string $key, bool $withTotal): array
    {
        if ($withTotal) {
            return ['value' => 0, 'accuracy' => 'exact'];
        }

        if (!is_file($this->file)) {
            return ['value' => 0, 'accuracy' => 'approx'];
        }

        $data = json_decode((string) file_get_contents($this->file), true);
        if (!is_array($data)) {
            return ['value' => 0, 'accuracy' => 'approx'];
        }

        return ['value' => (int) ($data[$key] ?? 0), 'accuracy' => 'approx'];
    }
}
