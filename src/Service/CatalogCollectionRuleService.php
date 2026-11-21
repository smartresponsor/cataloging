<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\ServiceInterface\CatalogCollectionRuleServiceInterface;

/**
 * Provides the collection rule service application service.
 */
final class CatalogCollectionRuleService implements CatalogCollectionRuleServiceInterface
{
    /** @param array<string,mixed> $dsl @return list<array<string,mixed>> */
    public function evaluate(array $dsl, int $limit): array
    {
        return [];
    }
}
