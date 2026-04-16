<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service\Category\Graphql;

use App\ServiceInterface\Category\CategoryGraphqlGuardInterface;

/**
 * Provides the category graphql guard application service.
 */
final readonly class CategoryGraphqlGuard implements CategoryGraphqlGuardInterface
{
    /**
     * Initializes the category graphql guard service collaborators.
     */
    public function __construct(private int $maxDepth = 8, private int $maxCost = 5000)
    {
    }

    /**
     * @param array<string, mixed> $ast
     *
     * @return array{depth: int, cost: int}
     */
    public function analyze(array $ast): array
    {
        $depth = $this->walkDepth($ast);
        $cost = $this->walkCost($ast);
        if ($depth > $this->maxDepth) {
            throw new \RuntimeException('GraphQL depth limit exceeded: '.$depth);
        }
        if ($cost > $this->maxCost) {
            throw new \RuntimeException('GraphQL cost limit exceeded: '.$cost);
        }

        return ['depth' => $depth, 'cost' => $cost];
    }

    /**
     * @param array<string,mixed> $node
     */
    private function walkDepth(array $node, int $level = 0): int
    {
        $max = $level;
        foreach ($this->childNodes($node) as $child) {
            $max = max($max, $this->walkDepth($child, $level + 1));
        }

        return $max;
    }

    /**
     * @param array<string,mixed> $node
     */
    private function walkCost(array $node): int
    {
        $cost = 1;
        foreach ($this->childNodes($node) as $child) {
            $cost += $this->walkCost($child);
        }

        return $cost;
    }

    /**
     * @param array<string,mixed> $node
     *
     * @return list<array<string,mixed>>
     */
    private function childNodes(array $node): array
    {
        $children = $node['children'] ?? [];
        if (!is_array($children)) {
            return [];
        }
        $normalized = [];
        foreach ($children as $child) {
            if (!is_array($child)) {
                continue;
            }

            $node = [];
            foreach ($child as $key => $value) {
                if (!is_string($key)) {
                    continue;
                }
                $node[$key] = $value;
            }
            $normalized[] = $node;
        }

        return $normalized;
    }
}
