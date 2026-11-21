<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ValueObject;

/**
 * Carries mutable state flags for category media binding workflows.
 */
final readonly class CatalogCategoryMediaBindingEntityState
{
    /**
     * @param array<string,mixed> $metadata
     */
    public function __construct(
        private bool $requiredForPublish,
        private bool $active,
        private array $metadata,
    ) {
    }

    public function requiredForPublish(): bool
    {
        return $this->requiredForPublish;
    }

    public function active(): bool
    {
        return $this->active;
    }

    /** @return array<string,mixed> */
    public function metadata(): array
    {
        return $this->metadata;
    }
}
if (!class_exists(__NAMESPACE__.'\\CategoryMediaBindingState', false)) {
    class_alias(CatalogCategoryMediaBindingEntityState::class, __NAMESPACE__.'\\CategoryMediaBindingState');
}
