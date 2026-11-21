<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ValueObject;

/**
 * Carries the primary identity and versioning context for package syndication workflows.
 */
final readonly class CategorySyndicationPackageContext
{
    /**
     * Initializes the category syndication package context value object.
     */
    public function __construct(
        private string $packageId,
        private string $destinationId,
        private string $categoryId,
        private string $version,
        private string $localeMode,
    ) {
    }

    public function packageId(): string
    {
        return $this->packageId;
    }

    public function destinationId(): string
    {
        return $this->destinationId;
    }

    public function categoryId(): string
    {
        return $this->categoryId;
    }

    public function version(): string
    {
        return $this->version;
    }

    public function localeMode(): string
    {
        return $this->localeMode;
    }
}
