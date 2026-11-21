<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

/**
 * Provides the collection builder application service.
 */
final readonly class CatalogCollectionBuilderService
{
    /**
     * Initializes the collection builder service collaborators.
     */
    public function __construct(private CatalogCollectionRuleEngineService $engine)
    {
    }

    /**
     * @param list<array<string, list<bool|float|int|string>|bool|float|int|string|null>> $all
     * @param array<string,list<bool|float|int|string>|bool|float|int|string|null>        $rules
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
