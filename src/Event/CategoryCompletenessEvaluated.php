<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Event;

use App\EventInterface\CategoryCompletenessEvaluatedInterface;

/**
 * Represents the category completeness evaluated application event.
 */
final readonly class CategoryCompletenessEvaluated implements CategoryCompletenessEvaluatedInterface
{
    /**
     * @param list<string>       $missingRequired
     * @param list<string>       $warnings
     * @param array<string,bool> $checks
     * @param array<string,bool> $publicationChecks
     */
    public function __construct(
        private string $categoryId,
        private int $score,
        private bool $complete,
        private array $missingRequired,
        private array $warnings,
        private array $checks,
        private array $publicationChecks,
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
