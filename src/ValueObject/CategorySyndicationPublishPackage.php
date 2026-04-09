<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObject;

use App\ValueObjectInterface\CategorySyndicationPublishPackageInterface;
/**
 * Represents the category syndication publish package value.
 */
final class CategorySyndicationPublishPackage implements CategorySyndicationPublishPackageInterface
{
    /**
     * @param array<string,mixed> $payload
     * @param list<string>        $missingRequiredFields
     */
    public function __construct(
        private readonly string $packageId,
        private readonly string $destinationId,
        private readonly string $categoryId,
        private readonly string $version,
        private readonly string $localeMode,
        private readonly array $payload,
        private readonly array $missingRequiredFields,
        private readonly bool $publishable,
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
