<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

final class ReadOptimizer
{
    /** @var array{tree?:list<array{id:string,name:string,slug:string,locale:string,published:bool}>} */
    private array $cache = [];
    private int $hit = 0;
    private int $miss = 0;

    /** @return list<array{id:string,name:string,slug:string,locale:string,published:bool}> */
    public function getTree(): array
    {
        $cachedTree = $this->cache['tree'] ?? null;
        if (is_array($cachedTree)) {
            ++$this->hit;
            $this->flushMetrics();

            return $cachedTree;
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
            'report/category-perf-read.json',
            json_encode([
                'hit' => $this->hit,
                'miss' => $this->miss,
                'ts' => date(DATE_ATOM),
            ], JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR)
        );
    }

    public function clear(): void
    {
        $this->cache = [];
    }
}
