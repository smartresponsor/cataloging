<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Service\Category;

final class ProjectionRunner
{
    public function run(array $nodes): array
    {
        $out = [];
        foreach ($nodes as $n) {
            $n['path'] = $n['path'] ?? '/'.$n['id'];
            $out[] = $n;
        }

        return $out;
    }
}
