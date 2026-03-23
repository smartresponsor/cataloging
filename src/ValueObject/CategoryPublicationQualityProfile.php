<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObject;

use App\ValueObjectInterface\CategoryPublicationQualityProfileInterface;

final class CategoryPublicationQualityProfile implements CategoryPublicationQualityProfileInterface
{
    /**
     * @param list<string>       $hardBlockers
     * @param list<string>       $softWarnings
     * @param list<string>       $advisoryWarnings
     * @param array<string,bool> $publicationChecks
     * @param array<string,bool> $checks
     */
    public function __construct(
        private readonly int $score,
        private readonly array $hardBlockers,
        private readonly array $softWarnings,
        private readonly array $advisoryWarnings,
        private readonly array $publicationChecks,
        private readonly array $checks,
        private readonly string $riskLevel,
    ) {
    }

    public function score(): int
    {
        return $this->score;
    }

    public function isPublishableQuality(): bool
    {
        return [] === $this->hardBlockers;
    }

    public function riskLevel(): string
    {
        return $this->riskLevel;
    }

    public function hardBlockers(): array
    {
        return $this->hardBlockers;
    }

    public function softWarnings(): array
    {
        return $this->softWarnings;
    }

    public function advisoryWarnings(): array
    {
        return $this->advisoryWarnings;
    }

    public function publicationChecks(): array
    {
        return $this->publicationChecks;
    }

    public function checks(): array
    {
        return $this->checks;
    }
}
