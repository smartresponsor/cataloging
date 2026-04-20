<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ValueObject;

/**
 * Carries the full input surface for category evaluation workflows.
 */
final readonly class CategoryEvaluationRequest
{
    /**
     * @param array<string,mixed> $payload
     */
    public function __construct(
        private string $categoryId,
        private array $payload,
        private CatalogAuditContext $auditContext,
    ) {
    }

    public function categoryId(): string
    {
        return $this->categoryId;
    }

    /** @return array<string,mixed> */
    public function payload(): array
    {
        return $this->payload;
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
