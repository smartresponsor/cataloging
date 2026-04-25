<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ValueObject;

/**
 * Carries the destination syndication governance summary request surface.
 */
final readonly class CatalogSyndicationDestinationGovernanceSummaryRequest
{
    /**
     * @param list<array<string, mixed>> $trailPayloads
     */
    public function __construct(
        private string $destinationId,
        private array $trailPayloads,
        private string $actorId,
        private string $reason,
    ) {
    }

    public function destinationId(): string
    {
        return $this->destinationId;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function trailPayloads(): array
    {
        return $this->trailPayloads;
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
if (!class_exists(__NAMESPACE__.'\\SyndicationDestinationGovernanceSummaryRequest', false)) {
    class_alias(CatalogSyndicationDestinationGovernanceSummaryRequest::class, __NAMESPACE__.'\\SyndicationDestinationGovernanceSummaryRequest');
}
