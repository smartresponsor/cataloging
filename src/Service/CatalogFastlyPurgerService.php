<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

/**
 * Provides the fastly purger application service.
 */
final class CatalogFastlyPurgerService
{
    /** @param list<string> $keys @return string */
    public function headerForKeys(array $keys): string
    {
        return implode(' ', array_values(array_unique($keys)));
    }
}
