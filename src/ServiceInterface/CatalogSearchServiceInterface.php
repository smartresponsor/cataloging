<?php

declare(strict_types=1);

namespace App\Cataloging\ServiceInterface;

use App\Cataloging\ValueObject\CategoryProjectionCriteria;

/**
 * Exposes the catalog projection search/read boundary to storefront consumers.
 */
interface CatalogSearchServiceInterface
{
    /** @return array<string, mixed> */
    public function search(?CategoryProjectionCriteria $criteria = null): array;
}
