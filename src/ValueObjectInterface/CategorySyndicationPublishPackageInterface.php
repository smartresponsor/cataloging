<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\ValueObjectInterface;

interface CategorySyndicationPublishPackageInterface
{
    public function packageId(): string;

    public function destinationId(): string;

    public function categoryId(): string;

    public function version(): string;

    public function localeMode(): string;

    /**
     * @return array<string,mixed>
     */
    public function payload(): array;

    /**
     * @return list<string>
     */
    public function missingRequiredFields(): array;

    public function publishable(): bool;
}
