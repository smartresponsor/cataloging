<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

use App\Entity\CategoryChangeRequest;
use App\Event\CategoryChangeRequestReviewed;
/**
 * Defines the contract for catalog change request service.
 */
interface CatalogChangeRequestServiceInterface
{
    /** @param array<string,mixed> $changes */
    public function submit(string $requestId, string $categoryId, string $submittedBy, string $summary, array $changes): CategoryChangeRequest;
    /**
     * Handles the review workflow.
     */
    public function review(string $requestId, string $targetState, string $reviewedBy, string $decisionReason): CategoryChangeRequestReviewed;
}
