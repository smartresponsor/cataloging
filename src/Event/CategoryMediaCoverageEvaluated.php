<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Event;

use App\Cataloging\EventInterface\CategoryMediaCoverageEvaluatedInterface;

/**
 * Represents the category media coverage evaluated application event.
 */
final readonly class CategoryMediaCoverageEvaluated implements CategoryMediaCoverageEvaluatedInterface
{
    /**
     * @param list<string>       $requiredMissing
     * @param list<string>       $warnings
     * @param array<string,bool> $checks
     */
    public function __construct(
        private string $categoryId,
        private array $requiredMissing,
        private array $warnings,
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
            'requiredMissing' => $this->requiredMissing,
            'warnings' => $this->warnings,
            'checks' => $this->checks,
            'actorId' => $this->actorId,
            'reason' => $this->reason,
            'evaluatedAt' => $this->evaluatedAt->format(DATE_ATOM),
        ];
    }
}
