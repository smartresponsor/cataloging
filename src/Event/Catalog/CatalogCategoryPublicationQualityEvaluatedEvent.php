<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Event\Catalog;

use App\Cataloging\EventInterface\Catalog\CatalogCategoryPublicationQualityEvaluatedEventInterface;

/**
 * Represents the category publication quality evaluated application event.
 */
final readonly class CatalogCategoryPublicationQualityEvaluatedEvent implements CatalogCategoryPublicationQualityEvaluatedEventInterface
{
    /**
     * @param list<string>       $hardBlockers
     * @param list<string>       $softWarnings
     * @param list<string>       $advisoryWarnings
     * @param array<string,bool> $publicationChecks
     * @param array<string,bool> $checks
     */
    public function __construct(
        private string $categoryId,
        private int $score,
        private bool $publishableQuality,
        private string $riskLevel,
        private array $hardBlockers,
        private array $softWarnings,
        private array $advisoryWarnings,
        private array $publicationChecks,
        private array $checks,
        private string $actorId,
        private string $reason,
        private \DateTimeImmutable $evaluatedAt,
    ) {
    }

    /** @return array<string,mixed> */
    public function payload(): array
    {
        return [
            'categoryId' => $this->categoryId,
            'score' => $this->score,
            'publishableQuality' => $this->publishableQuality,
            'riskLevel' => $this->riskLevel,
            'hardBlockers' => $this->hardBlockers,
            'softWarnings' => $this->softWarnings,
            'advisoryWarnings' => $this->advisoryWarnings,
            'publicationChecks' => $this->publicationChecks,
            'checks' => $this->checks,
            'actorId' => $this->actorId,
            'reason' => $this->reason,
            'evaluatedAt' => $this->evaluatedAt->format(DATE_ATOM),
        ];
    }
}
