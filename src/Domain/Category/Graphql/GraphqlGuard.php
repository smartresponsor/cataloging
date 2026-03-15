<?php

declare(strict_types=1);
/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@smartresponsor.com>
 * Owner: Marketing America Corp
 * Canon: single-hyphen names, no plurals in Class/Method, Postgres=Data, MySQL=Infrastructure
 * Tag Signin: <17111337+taa0662621456@users.noreply.github.com>
 */

namespace App\Service\CatalogCategory\Domain\Graphql;

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
