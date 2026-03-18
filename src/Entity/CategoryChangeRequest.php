<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Entity;

use App\EntityInterface\CategoryChangeRequestInterface;
use App\ValueObject\CategoryChangeRequestState;
use App\ValueObjectInterface\CategoryChangeRequestStateInterface;

final class CategoryChangeRequest implements CategoryChangeRequestInterface
{
    public function __construct(
        private readonly string $requestId,
        private readonly string $categoryId,
        private readonly string $submittedBy,
        private readonly string $summary,
        private readonly array $changes,
        private readonly CategoryChangeRequestState $state,
        private readonly ?string $reviewedBy,
        private readonly ?string $decisionReason,
        private readonly \DateTimeImmutable $submittedAt,
        private readonly ?\DateTimeImmutable $reviewedAt,
    ) {
    }

    public static function open(string $requestId, string $categoryId, string $submittedBy, string $summary, array $changes): self
    {
        return new self(
            $requestId,
            $categoryId,
            $submittedBy,
            trim($summary),
            $changes,
            CategoryChangeRequestState::proposed(),
            null,
            null,
            new \DateTimeImmutable('now'),
            null,
        );
    }

    public function moveTo(CategoryChangeRequestState $state, string $reviewedBy, string $decisionReason): self
    {
        return new self(
            $this->requestId,
            $this->categoryId,
            $this->submittedBy,
            $this->summary,
            $this->changes,
            $state,
            trim($reviewedBy),
            trim($decisionReason),
            $this->submittedAt,
            new \DateTimeImmutable('now'),
        );
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

    public function changes(): array
    {
        return $this->changes;
    }

    public function state(): CategoryChangeRequestStateInterface
    {
        return $this->state;
    }

    public function reviewedBy(): ?string
    {
        return $this->reviewedBy;
    }

    public function decisionReason(): ?string
    {
        return $this->decisionReason;
    }

    public function submittedAt(): \DateTimeImmutable
    {
        return $this->submittedAt;
    }

    public function reviewedAt(): ?\DateTimeImmutable
    {
        return $this->reviewedAt;
    }
}
