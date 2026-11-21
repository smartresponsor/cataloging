<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ValueObject;

use App\Cataloging\EntityInterface\Catalog\CatalogSyndicationDeliveryRecordEntityInterface;

/**
 * Carries the destination syndication history and recovery audit request surface.
 */
final readonly class CategorySyndicationHistoryRequest
{
    /**
     * @param list<CatalogSyndicationDeliveryRecordEntityInterface> $records
     */
    public function __construct(
        private string $destinationId,
        private array $records,
        private string $actorId,
        private string $reason,
    ) {
    }

    public function destinationId(): string
    {
        return $this->destinationId;
    }

    /**
     * @return list<CatalogSyndicationDeliveryRecordEntityInterface>
     */
    public function records(): array
    {
        return $this->records;
    }

    public function actorId(): string
    {
        return $this->actorId;
    }

    public function reason(): string
    {
        return $this->reason;
    }
}
