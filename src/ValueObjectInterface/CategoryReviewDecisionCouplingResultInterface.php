<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ValueObjectInterface;

/**
 * Defines the contract for category review decision coupling result.
 */
interface CategoryReviewDecisionCouplingResultInterface
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
     * Handles the review state workflow.
     */
    public function reviewState(): string;

    /**
     * Handles the workflow state workflow.
     */
    public function workflowState(): string;

    /**
     * Handles the publishable workflow.
     */
    public function publishable(): bool;

    /** @return list<string> */
    public function blockers(): array;

    /** @return list<string> */
    public function warnings(): array;

    /** @return array<string,bool> */
    public function checks(): array;

    /**
     * Handles the actor id workflow.
     */
    public function actorId(): string;

    /**
     * Handles the reason workflow.
     */
    public function reason(): string;

    /** @return array<string,mixed> */
    public function payload(): array;
}
