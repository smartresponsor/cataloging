<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;
/**
 * Provides the tree consistency checker application service.
 */
final class TreeConsistencyChecker
{
    /**
     * @param list<array{id?:mixed,level?:mixed}> $nodes
     *
     * @return list<array{error:string,node?:array{id?:mixed,level?:mixed},id?:mixed}>
     */
    public function check(array $nodes): array
    {
        $errors = [];
        foreach ($nodes as $node) {
            if (!isset($node['id'])) {
                $errors[] = ['error' => 'missing_id', 'node' => $node];
            }
            if (isset($node['level']) && is_numeric($node['level']) && (int) $node['level'] < 0) {
                $errors[] = ['error' => 'negative_level', 'id' => $node['id'] ?? null];
            }
        }

        return $errors;
    }
}
