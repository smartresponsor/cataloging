<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Service;

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
            ['id' => '1', 'name' => 'Root', 'slug' => 'root', 'locale' => 'en', 'channel' => 'default', 'published' => true],
            ['id' => '2', 'name' => 'Electronics', 'slug' => 'electronics', 'locale' => 'en', 'channel' => 'default', 'published' => true],
            ['id' => '3', 'name' => 'Phones', 'slug' => 'phones', 'locale' => 'uk', 'channel' => 'default', 'published' => true],
            ['id' => '4', 'name' => 'Hidden', 'slug' => 'hidden', 'locale' => 'en', 'channel' => 'beta', 'published' => false],
        ];
        $this->cache['tree'] = $tree;
        $this->flushMetrics();

        return $tree;
    }

    private function flushMetrics(): void
    {
        @mkdir('report', 0777, true);
        file_put_contents(
            'report/category-perf-read.json',
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
