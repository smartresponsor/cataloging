<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObjectInterface;

/**
 * Defines the contract for category syndication mapping profile.
 */
interface CategorySyndicationMappingProfileInterface
{
    /**
     * Handles the destination id workflow.
     */
    public function destinationId(): string;

    /**
     * Handles the version workflow.
     */
    public function version(): string;

    /**
     * @return array<string,string>
     */
    public function fieldMap(): array;

    /**
     * @return list<string>
     */
    public function requiredFields(): array;

    /**
     * Handles the locale mode workflow.
     */
    public function localeMode(): string;
}
