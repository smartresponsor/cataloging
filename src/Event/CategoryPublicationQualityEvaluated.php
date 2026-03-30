<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Event;

use App\EventInterface\CategoryPublicationQualityEvaluatedInterface;

final class CategoryPublicationQualityEvaluated implements CategoryPublicationQualityEvaluatedInterface
{
    /**
     * @param list<string>       $hardBlockers
     * @param list<string>       $softWarnings
     * @param list<string>       $advisoryWarnings
     * @param array<string,bool> $publicationChecks
     * @param array<string,bool> $checks
     */
    public function __construct(
        private readonly string $categoryId,
        private readonly int $score,
        private readonly bool $publishableQuality,
        private readonly string $riskLevel,
        private readonly array $hardBlockers,
        private readonly array $softWarnings,
        private readonly array $advisoryWarnings,
        private readonly array $publicationChecks,
        private readonly array $checks,
        private readonly string $actorId,
        private readonly string $reason,
        private readonly \DateTimeImmutable $evaluatedAt,
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
