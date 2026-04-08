<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Entity;

use App\EntityInterface\CategoryChangeRequestInterface;
use App\ValueObject\CategoryChangeRequestState;
use App\ValueObjectInterface\CategoryChangeRequestStateInterface;
/**
 * Represents the category change request domain record.
 */
final class CategoryChangeRequest implements CategoryChangeRequestInterface
{
    /**
     * @param array<string,mixed> $changes
     */
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

    /** @param array<string,mixed> $changes */
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
    /**
     * Handles the move to workflow.
     */
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
    /**
     * Handles the request id workflow.
     */
    public function requestId(): string
    {
        return $this->requestId;
    }
    /**
     * Handles the category id workflow.
     */
    public function categoryId(): string
    {
        return $this->categoryId;
    }
    /**
     * Handles the submitted by workflow.
     */
    public function submittedBy(): string
    {
        return $this->submittedBy;
    }
    /**
     * Handles the summary workflow.
     */
    public function summary(): string
    {
        return $this->summary;
    }

    /** @return array<string,mixed> */
    public function changes(): array
    {
        return $this->changes;
    }
    /**
     * Handles the state workflow.
     */
    public function state(): CategoryChangeRequestStateInterface
    {
        return $this->state;
    }
    /**
     * Handles the reviewed by workflow.
     */
    public function reviewedBy(): ?string
    {
        return $this->reviewedBy;
    }
    /**
     * Handles the decision reason workflow.
     */
    public function decisionReason(): ?string
    {
        return $this->decisionReason;
    }
    /**
     * Handles the submitted at workflow.
     */
    public function submittedAt(): \DateTimeImmutable
    {
        return $this->submittedAt;
    }
    /**
     * Handles the reviewed at workflow.
     */
    public function reviewedAt(): ?\DateTimeImmutable
    {
        return $this->reviewedAt;
    }
}
