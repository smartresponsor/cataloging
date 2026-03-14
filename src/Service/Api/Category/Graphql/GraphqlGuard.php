<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

declare(strict_types=1);

namespace App\Service\Api\Category\Graphql;

class GraphqlGuard
{
    public function __construct(
        private readonly int $maxDepth = 8,
        private readonly int $maxCost = 5000,
    ) {
    }

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

    private function walkDepth(array $node, int $level = 0): int
    {
        $max = $level;
        foreach (($node['children'] ?? []) as $child) {
            if (is_array($child)) {
                $max = max($max, $this->walkDepth($child, $level + 1));
            }
        }

        return $max;
    }

    private function walkCost(array $node): int
    {
        $cost = 1;
        foreach (($node['children'] ?? []) as $child) {
            if (is_array($child)) {
                $cost += $this->walkCost($child);
            }
        }

        return $cost;
    }
}
