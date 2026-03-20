<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\ValueObjectInterface;

interface CategorySyndicationMappingProfileInterface
{
    public function destinationId(): string;

    public function version(): string;

    /**
     * @return array<string,string>
     */
    public function fieldMap(): array;

    /**
     * @return list<string>
     */
    public function requiredFields(): array;

    public function localeMode(): string;
}
