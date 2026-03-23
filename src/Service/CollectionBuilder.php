<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

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
