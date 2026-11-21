<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ValueObject;

/**
 * Carries the category syndication governance summary request surface.
 */
final readonly class CategorySyndicationGovernanceSummaryRequest
{
    /**
     * @param list<array<string, mixed>> $trailPayloads
     */
    public function __construct(
        private string $categoryId,
        private array $trailPayloads,
        private string $actorId,
        private string $reason,
    ) {
    }

    public function categoryId(): string
    {
        return $this->categoryId;
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
