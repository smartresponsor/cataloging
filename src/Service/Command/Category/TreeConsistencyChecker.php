<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Service\Command\Category;

final class TreeConsistencyChecker
{
    public function check(array $nodes): array
    {
        $errors = [];
        foreach ($nodes as $node) {
            if (!isset($node['id'])) {
                $errors[] = ['error' => 'missing_id', 'node' => $node];
            }
            if (isset($node['level']) && $node['level'] < 0) {
                $errors[] = ['error' => 'negative_level', 'id' => $node['id']];
            }
        }

        return $errors;
    }
}
