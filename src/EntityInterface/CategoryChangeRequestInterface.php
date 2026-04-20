<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\EntityInterface;

use App\Cataloging\ValueObjectInterface\CategoryChangeRequestStateInterface;

/**
 * Defines the contract for category change request.
 */
interface CategoryChangeRequestInterface
{
    /**
     * Handles the request id workflow.
     */
    public function requestId(): string;

    /**
     * Handles the category id workflow.
     */
    public function categoryId(): string;

    /**
     * Handles the submitted by workflow.
     */
    public function submittedBy(): string;

    /**
     * Handles the summary workflow.
     */
    public function summary(): string;

    /** @return array<string,mixed> */
    public function changes(): array;

    /**
     * Handles the state workflow.
     */
    public function state(): CategoryChangeRequestStateInterface;

    /**
     * Handles the reviewed by workflow.
     */
    public function reviewedBy(): ?string;

    /**
     * Handles the decision reason workflow.
     */
    public function decisionReason(): ?string;

    /**
     * Handles the submitted at workflow.
     */
    public function submittedAt(): \DateTimeImmutable;

    /**
     * Handles the reviewed at workflow.
     */
    public function reviewedAt(): ?\DateTimeImmutable;
}
