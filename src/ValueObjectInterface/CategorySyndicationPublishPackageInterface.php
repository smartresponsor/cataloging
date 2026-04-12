<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObjectInterface;

/**
 * Defines the contract for category syndication publish package.
 */
interface CategorySyndicationPublishPackageInterface
{
    /**
     * Handles the package id workflow.
     */
    public function packageId(): string;

    /**
     * Handles the destination id workflow.
     */
    public function destinationId(): string;

    /**
     * Handles the category id workflow.
     */
    public function categoryId(): string;

    /**
     * Handles the version workflow.
     */
    public function version(): string;

    /**
     * Handles the locale mode workflow.
     */
    public function localeMode(): string;

    /**
     * @return array<string,mixed>
     */
    public function payload(): array;

    /**
     * @return list<string>
     */
    public function missingRequiredFields(): array;

    /**
     * Handles the publishable workflow.
     */
    public function publishable(): bool;
}
