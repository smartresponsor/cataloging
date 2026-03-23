<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service\Category\Graphql;

final class GraphqlGuard
{
    private int $maxDepth;
    private int $maxCost;

    public function __construct(int $maxDepth = 8, int $maxCost = 5000)
    {
        $this->maxDepth = $maxDepth;
        $this->maxCost = $maxCost;
    }

    /**
     * Compute a naive cost score and depth for a GraphQL query AST (pseudo-interface).
     * Integrate with webonyx/graphql-php visitor in your server adapter.
     *
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

    private function walkDepth(array $node, int $level = 0): int
    {
        $max = $level;
        foreach (($node['children'] ?? []) as $c) {
            $max = max($max, $this->walkDepth($c, $level + 1));
        }

        return $max;
    }

    private function walkCost(array $node): int
    {
        $cost = 1;
        foreach (($node['children'] ?? []) as $c) {
            $cost += $this->walkCost($c);
        }

        return $cost;
    }
}
