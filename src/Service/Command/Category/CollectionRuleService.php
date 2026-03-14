<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@smartresponsor.com>.
 */
declare(strict_types=1);

namespace App\Service\Command\Category;

use App\ServiceInterface\Command\Category\CollectionRuleServiceInterface;

final class CollectionRuleService implements CollectionRuleServiceInterface
{
    public function evaluate(array $dsl, int $limit): array
    {
        // This method should translate DSL into a query plan against product index.
        // Keep it deterministic and cache-aware.
        return [];
    }
}
