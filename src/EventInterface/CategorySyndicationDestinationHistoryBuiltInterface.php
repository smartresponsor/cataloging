<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\EventInterface;

interface CategorySyndicationDestinationHistoryBuiltInterface
{
    /** @return array<string,mixed> */
    public function payload(): array;

    public function occurredAt(): \DateTimeImmutable;
}
