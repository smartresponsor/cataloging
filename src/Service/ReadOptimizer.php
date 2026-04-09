<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\ServiceInterface\CategoryProjectionReadServiceInterface;

/**
 * Provides the read optimizer application service.
 */
final class ReadOptimizer
{
    /** @var array<string,list<array<string,mixed>>> */
    private array $cache = [];
    private int $hit = 0;
    private int $miss = 0;

    /**
     * Initializes the read optimizer service collaborators.
     */
    public function __construct(private readonly CategoryProjectionReadServiceInterface $categoryProjectionReadService)
    {
    }

    /**
     * @param array<string,mixed> $criteria
     *
     * @return list<array<string,mixed>>
     *
     * @throws \JsonException
     */
    public function getTree(array $criteria = []): array
    {
        $cacheKey = $this->cacheKey($criteria);
        $cachedTree = $this->cache[$cacheKey] ?? null;
        if (is_array($cachedTree)) {
            ++$this->hit;

            return $cachedTree;
        }

        ++$this->miss;
        $tree = array_map(
            static function (array $row): array {
                if (!array_key_exists('channel', $row)) {
                    $row['channel'] = 'default';
                }

                return $row;
            },
            $this->categoryProjectionReadService->tree($criteria),
        );
        $this->cache[$cacheKey] = $tree;

        return $tree;
    }

    /** @return array{hit:int,miss:int,size:int} */
    public function stats(): array
    {
        return [
            'hit' => $this->hit,
            'miss' => $this->miss,
            'size' => count($this->cache),
        ];
    }

    /**
     * Handles the clear workflow.
     */
    public function clear(): void
    {
        $this->cache = [];
    }

    /**
     * @param array<string,mixed> $criteria
     *
     * @return string
     *
     * @throws \JsonException
     */
    private function cacheKey(array $criteria): string
    {
        ksort($criteria);

        return sha1(json_encode($criteria, JSON_THROW_ON_ERROR));
    }
}
