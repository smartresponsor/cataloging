<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

final class CacheMetricsCollector
{
    /** @var array<string,int> */
    private array $hits = [];
    /** @var array<string,int> */
    private array $misses = [];

    public function hit(string $locale): void
    {
        $this->hits[$locale] = ($this->hits[$locale] ?? 0) + 1;
    }

    public function miss(string $locale): void
    {
        $this->misses[$locale] = ($this->misses[$locale] ?? 0) + 1;
    }

    public function dump(string $file): void
    {
        file_put_contents($file, json_encode(['hits' => $this->hits, 'misses' => $this->misses], JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));
    }
}
