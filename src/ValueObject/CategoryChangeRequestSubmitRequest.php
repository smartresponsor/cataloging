<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ValueObject;

/**
 * Carries the full input surface for category change request submission workflows.
 */
final readonly class CategoryChangeRequestSubmitRequest
{
    /** @param array<string,mixed> $changes */
    public function __construct(
        private string $requestId,
        private string $categoryId,
        private string $submittedBy,
        private string $summary,
        private array $changes,
    ) {
    }

    public function requestId(): string
    {
        return $this->requestId;
    }

    public function categoryId(): string
    {
        return $this->categoryId;
    }

    public function submittedBy(): string
    {
        return $this->submittedBy;
    }

    public function summary(): string
    {
        return $this->summary;
    }

    /** @return array<string,mixed> */
    public function changes(): array
    {
        return $this->changes;
    }
}
