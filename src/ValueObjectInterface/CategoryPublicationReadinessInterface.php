<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\ValueObjectInterface;

interface CategoryPublicationReadinessInterface
{
    public function isPublishable(): bool;

    public function hasCheck(string $name): bool;

    public function check(string $name): bool;

    /** @return list<string> */
    public function blockers(): array;

    /** @return list<string> */
    public function warnings(): array;

    /** @return array<string,bool> */
    public function checks(): array;
}
