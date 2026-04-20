<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ValueObject;

use App\Cataloging\ValueObjectInterface\CategorySyndicationMappingProfileInterface;

/**
 * Represents the category syndication mapping profile value.
 */
final readonly class CategorySyndicationMappingProfile implements CategorySyndicationMappingProfileInterface
{
    /**
     * @param array<string,string> $fieldMap
     * @param list<string>         $requiredFields
     */
    public function __construct(
        private string $destinationId,
        private string $version,
        private array $fieldMap,
        private array $requiredFields,
        private string $localeMode,
    ) {
    }

    /**
     * Handles the destination id workflow.
     */
    public function destinationId(): string
    {
        return $this->destinationId;
    }

    /**
     * Handles the version workflow.
     */
    public function version(): string
    {
        return $this->version;
    }

    /**
     * Handles the field map workflow.
     */
    public function fieldMap(): array
    {
        return $this->fieldMap;
    }

    /**
     * Handles the required fields workflow.
     */
    public function requiredFields(): array
    {
        return $this->requiredFields;
    }

    /**
     * Handles the locale mode workflow.
     */
    public function localeMode(): string
    {
        return $this->localeMode;
    }
}
