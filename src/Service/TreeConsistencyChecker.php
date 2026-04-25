<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

/**
 * Provides the tree consistency checker application service.
 */
final class TreeConsistencyChecker
{
    /**
     * @param list<array{id?:mixed,depth?:mixed,level?:mixed}> $nodes
     *
     * @return list<array{error:string,node?:array{id?:mixed,depth?:mixed,level?:mixed},id?:mixed}>
     */
    public function check(array $nodes): array
    {
        $errors = [];
        foreach ($nodes as $node) {
            if (!isset($node['id'])) {
                $errors[] = ['error' => 'missing_id', 'node' => $node];
            }
            $depth = $node['depth'] ?? $node['level'] ?? null;
            if (is_numeric($depth) && (int) $depth < 0) {
                $errors[] = ['error' => 'negative_depth', 'id' => $node['id'] ?? null];
            }
        }

        return $errors;
    }
}
