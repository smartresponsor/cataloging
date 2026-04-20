<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ValueObject;

use App\Cataloging\ValueObjectInterface\CategoryPublicationQualityProfileInterface;

/**
 * Represents the category publication quality profile value.
 */
final readonly class CategoryPublicationQualityProfile implements CategoryPublicationQualityProfileInterface
{
    /**
     * @param list<string>       $hardBlockers
     * @param list<string>       $softWarnings
     * @param list<string>       $advisoryWarnings
     * @param array<string,bool> $publicationChecks
     * @param array<string,bool> $checks
     */
    public function __construct(
        private int $score,
        private array $hardBlockers,
        private array $softWarnings,
        private array $advisoryWarnings,
        private array $publicationChecks,
        private array $checks,
        private string $riskLevel,
    ) {
    }

    /**
     * Handles the score workflow.
     */
    public function score(): int
    {
        return $this->score;
    }

    /**
     * Determines whether the publishable quality condition is satisfied.
     */
    public function isPublishableQuality(): bool
    {
        return [] === $this->hardBlockers;
    }

    /**
     * Handles the risk level workflow.
     */
    public function riskLevel(): string
    {
        return $this->riskLevel;
    }

    /**
     * Handles the hard blockers workflow.
     */
    public function hardBlockers(): array
    {
        return $this->hardBlockers;
    }

    /**
     * Handles the soft warnings workflow.
     */
    public function softWarnings(): array
    {
        return $this->softWarnings;
    }

    /**
     * Handles the advisory warnings workflow.
     */
    public function advisoryWarnings(): array
    {
        return $this->advisoryWarnings;
    }

    /**
     * Handles the publication checks workflow.
     */
    public function publicationChecks(): array
    {
        return $this->publicationChecks;
    }

    /**
     * Handles the checks workflow.
     */
    public function checks(): array
    {
        return $this->checks;
    }
}
