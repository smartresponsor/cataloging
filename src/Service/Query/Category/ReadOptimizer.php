<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Service\Query\Category;

final class ReadOptimizer
{
    private array $cache = [];
    private int $hit = 0;
    private int $miss = 0;

    public function getTree(): array
    {
        if (isset($this->cache['tree'])) {
            ++$this->hit;
            $this->flushMetrics();

            return $this->cache['tree'];
        }
        ++$this->miss;
        $tree = [
            ['id' => '1', 'name' => 'Root', 'slug' => 'root', 'locale' => 'en', 'published' => true],
            ['id' => '2', 'name' => 'Electronics', 'slug' => 'electronics', 'locale' => 'en', 'published' => true],
        ];
        $this->cache['tree'] = $tree;
        $this->flushMetrics();

        return $tree;
    }

    private function flushMetrics(): void
    {
        file_put_contents(
            'report/catalog-perf-read.json',
            json_encode([
                'hit' => $this->hit,
                'miss' => $this->miss,
                'ts' => date(DATE_ATOM),
            ], JSON_PRETTY_PRINT)
        );
    }

    public function clear(): void
    {
        $this->cache = [];
    }
}
