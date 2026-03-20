<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\ValueObject;

use App\ValueObjectInterface\CategorySyndicationMappingProfileInterface;

final class CategorySyndicationMappingProfile implements CategorySyndicationMappingProfileInterface
{
    /**
     * @param array<string,string> $fieldMap
     * @param list<string>         $requiredFields
     */
    public function __construct(
        private readonly string $destinationId,
        private readonly string $version,
        private readonly array $fieldMap,
        private readonly array $requiredFields,
        private readonly string $localeMode,
    ) {
    }

    public function destinationId(): string
    {
        return $this->destinationId;
    }

    public function version(): string
    {
        return $this->version;
    }

    public function fieldMap(): array
    {
        return $this->fieldMap;
    }

    public function requiredFields(): array
    {
        return $this->requiredFields;
    }

    public function localeMode(): string
    {
        return $this->localeMode;
    }
}
