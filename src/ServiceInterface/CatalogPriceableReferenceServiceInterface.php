<?php

declare(strict_types=1);

namespace App\Cataloging\ServiceInterface;

/** Exposes stable opaque Cataloging resource references for external pricing ownership. */
interface CatalogPriceableReferenceServiceInterface
{
    /** Returns the stable Pricing-facing reference for a catalog record identity. */
    public function forRecord(string $recordId): string;
}
