<?php

declare(strict_types=1);

namespace App\Cataloging\ServiceInterface;

/**
 * Reads one published category tree scoped to a durable catalog code.
 */
interface CatalogCatalogTreeReadServiceInterface
{
    /** @return array<string, mixed>|null */
    public function byCode(string $catalogCode, string $tenant = 'default'): ?array;
}
