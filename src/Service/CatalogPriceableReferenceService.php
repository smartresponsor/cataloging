<?php

declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\ServiceInterface\CatalogPriceableReferenceServiceInterface;

/** Builds Cataloging-owned resource references without embedding pricing behavior. */
final class CatalogPriceableReferenceService implements CatalogPriceableReferenceServiceInterface
{
    public function forRecord(string $recordId): string
    {
        $recordId = trim($recordId);
        if ('' === $recordId) {
            throw new \InvalidArgumentException('Catalog record id must not be empty.');
        }

        return 'catalog:record:'.$recordId;
    }
}
