<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Event\Catalog;

/** Emitted after move/reorder. */
final class CatalogCategoryReorderedEvent
{
    /** @var array<string,mixed> */
    private array $payload;

    /** @param array<string,mixed> $payload */
    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }

    /** @return array<string,mixed> */
    public function payload(): array
    {
        return $this->payload;
    }
}
