<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Service\Query\Category;

final class CollectionBuilder
{
    public function __construct(private readonly RuleEngine $engine)
    {
    }

    public function build(array $all, array $rules): array
    {
        $out = [];
        foreach ($all as $cat) {
            if ($this->engine->match($cat, $rules)) {
                $out[] = $cat;
            }
        }

        return $out;
    }
}
