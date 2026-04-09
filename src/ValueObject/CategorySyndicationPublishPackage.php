<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObject;

use App\ValueObjectInterface\CategorySyndicationPublishPackageInterface;

/**
 * Represents the category syndication publish package value.
 */
final readonly class CategorySyndicationPublishPackage implements CategorySyndicationPublishPackageInterface
{
    /**
     * @param array<string,mixed> $payload
     * @param list<string>        $missingRequiredFields
     */
    public function __construct(
        private string $packageId,
        private string $destinationId,
        private string $categoryId,
        private string $version,
        private string $localeMode,
        private array $payload,
        private array $missingRequiredFields,
        private bool $publishable,
    ) {
    }

    /**
     * Handles the package id workflow.
     */
    public function packageId(): string
    {
        return $this->packageId;
    }

    /**
     * Handles the destination id workflow.
     */
    public function destinationId(): string
    {
        return $this->destinationId;
    }

    /**
     * Handles the category id workflow.
     */
    public function categoryId(): string
    {
        return $this->categoryId;
    }

    /**
     * Handles the version workflow.
     */
    public function version(): string
    {
        return $this->version;
    }

    /**
     * Handles the locale mode workflow.
     */
    public function localeMode(): string
    {
        return $this->localeMode;
    }

    /**
     * Handles the payload workflow.
     */
    public function payload(): array
    {
        return $this->payload;
    }

    /**
     * Handles the missing required fields workflow.
     */
    public function missingRequiredFields(): array
    {
        return $this->missingRequiredFields;
    }

    /**
     * Handles the publishable workflow.
     */
    public function publishable(): bool
    {
        return $this->publishable;
    }
}
