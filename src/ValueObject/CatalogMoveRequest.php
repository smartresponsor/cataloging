<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ValueObject;

/**
 * Carries the full input surface for catalog move workflows.
 */
final readonly class CatalogMoveRequest
{
    /**
     * Initializes the catalog move request value object.
     */
    public function __construct(
        private string $nodeId,
        private string $newParentId,
        private string $treeId,
        private string $policy,
        private bool $dryRun = false,
        private ?string $locale = null,
    ) {
    }

    public function nodeId(): string
    {
        return $this->nodeId;
    }

    public function newParentId(): string
    {
        return $this->newParentId;
    }

    public function treeId(): string
    {
        return $this->treeId;
    }

    public function policy(): string
    {
        return $this->policy;
    }

    public function dryRun(): bool
    {
        return $this->dryRun;
    }

    public function locale(): ?string
    {
        return $this->locale;
    }
}
