<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObject;

/**
 * Carries destination-scoped category media evaluation input.
 */
final readonly class CategoryDestinationMediaEvaluationRequest
{
    /**
     * Initializes destination media evaluation request state.
     */
    public function __construct(
        private string $destinationId,
        private string $categoryId,
        private CatalogAuditContext $auditContext,
    ) {
    }

    public function destinationId(): string
    {
        return trim($this->destinationId);
    }

    public function categoryId(): string
    {
        return trim($this->categoryId);
    }

    public function auditContext(): CatalogAuditContext
    {
        return $this->auditContext;
    }

    public function actorId(): string
    {
        return $this->auditContext->actorId();
    }

    public function reason(): string
    {
        return $this->auditContext->reason();
    }
}
