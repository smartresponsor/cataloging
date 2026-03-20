<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Event;

use App\EventInterface\CategoryMediaCoverageEvaluatedInterface;

final class CategoryMediaCoverageEvaluated implements CategoryMediaCoverageEvaluatedInterface
{
    /**
     * @param list<string>       $requiredMissing
     * @param list<string>       $warnings
     * @param array<string,bool> $checks
     */
    public function __construct(
        private readonly string $categoryId,
        private readonly array $requiredMissing,
        private readonly array $warnings,
        private readonly array $checks,
        private readonly string $actorId,
        private readonly string $reason,
        private readonly \DateTimeImmutable $evaluatedAt,
    ) {
    }

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
