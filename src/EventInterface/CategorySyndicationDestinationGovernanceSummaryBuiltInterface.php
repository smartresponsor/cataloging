<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\EventInterface;

interface CategorySyndicationDestinationGovernanceSummaryBuiltInterface
{
    public function payload(): array;

    public function occurredAt(): \DateTimeImmutable;
}
