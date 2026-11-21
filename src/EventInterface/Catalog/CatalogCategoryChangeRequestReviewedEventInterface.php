<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\EventInterface\Catalog;

/**
 * Defines the contract for category change request reviewed.
 */
interface CatalogCategoryChangeRequestReviewedEventInterface
{
    /** @return array<string,mixed> */
    public function payload(): array;

    /**
     * Returns the reviewed request identifier.
     */
    public function requestId(): string;
}
