<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;
/**
 * Provides the collection builder application service.
 */
final class CollectionBuilder
{
    /**
     * Initializes the collection builder service collaborators.
     */
    public function __construct(private readonly CollectionRuleEngine $engine)
    {
    }

    /**
     * @param list<array<string, list<bool|float|int|string>|bool|float|int|string|null>> $all
     * @param array<string,array<int,bool|float|int|string>|bool|float|int|string>         $rules
     *
     * @return list<array<string, list<bool|float|int|string>|bool|float|int|string|null>>
     */
    public function build(array $all, array $rules): array
    {
        $out = [];
        foreach ($all as $category) {
            if ($this->engine->match($category, $rules)) {
                $out[] = $category;
            }
        }

        return $out;
    }
}
