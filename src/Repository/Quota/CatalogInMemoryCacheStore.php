<?php

declare(strict_types=1);

/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 */

namespace App\Cataloging\Repository\Quota;

use App\Cataloging\ServiceInterface\Quota\CacheStoreInterface;

final class CatalogInMemoryCacheStore implements CacheStoreInterface
{
    /** @var array<string, array{value:string, expiresAt:int}> */
    private array $itemMap = [];

    public function get(string $key): ?string
    {
        $item = $this->itemMap[$key] ?? null;
        if (null === $item) {
            return null;
        }

        if ($item['expiresAt'] < time()) {
            unset($this->itemMap[$key]);

            return null;
        }

        return $item['value'];
    }

    public function set(string $key, string $value, int $ttl): void
    {
        $this->itemMap[$key] = [
            'value' => $value,
            'expiresAt' => time() + max(1, $ttl),
        ];
    }
}
