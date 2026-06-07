<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ValueObject;

/**
 * Carries canonical scope identifiers for category media binding workflows.
 */
final readonly class CatalogCategoryMediaBindingEntityScope
{
    /**
     * @param list<string> $channels
     * @param list<string> $locales
     */
    public function __construct(
        private string $bindingId,
        private string $categoryId,
        private string $assetId,
        private string $role,
        private array $channels,
        private array $locales,
    ) {
    }

    public function bindingId(): string
    {
        return $this->bindingId;
    }

    public function categoryId(): string
    {
        return $this->categoryId;
    }

    public function assetId(): string
    {
        return $this->assetId;
    }

    public function role(): string
    {
        return $this->role;
    }

    /** @return list<string> */
    public function channels(): array
    {
        return $this->channels;
    }

    /** @return list<string> */
    public function locales(): array
    {
        return $this->locales;
    }
}
if (!class_exists(__NAMESPACE__.'\\CategoryMediaBindingScope', false)) {
    class_alias(CatalogCategoryMediaBindingEntityScope::class, __NAMESPACE__.'\\CategoryMediaBindingScope');
}
