<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Event;

use App\EventInterface\CategoryCompletenessEvaluatedInterface;
/**
 * Represents the category completeness evaluated application event.
 */
final class CategoryCompletenessEvaluated implements CategoryCompletenessEvaluatedInterface
{
    /**
     * @param list<string>       $missingRequired
     * @param list<string>       $warnings
     * @param array<string,bool> $checks
     * @param array<string,bool> $publicationChecks
     */
    public function __construct(
        private readonly string $categoryId,
        private readonly int $score,
        private readonly bool $complete,
        private readonly array $missingRequired,
        private readonly array $warnings,
        private readonly array $checks,
        private readonly array $publicationChecks,
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
            'complete' => $this->complete,
            'missingRequired' => $this->missingRequired,
            'warnings' => $this->warnings,
            'checks' => $this->checks,
            'publicationChecks' => $this->publicationChecks,
            'actorId' => $this->actorId,
            'reason' => $this->reason,
            'evaluatedAt' => $this->evaluatedAt->format(DATE_ATOM),
        ];
    }
}
