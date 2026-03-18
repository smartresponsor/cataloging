<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Event;

use App\EventInterface\CategorySyndicationPackageGatedInterface;

final class CategorySyndicationPackageGated implements CategorySyndicationPackageGatedInterface
{
    /** @param array<string,mixed> $payload */
    public function __construct(
        private readonly array $payload,
        private readonly \DateTimeImmutable $occurredAt,
    ) {
    }

    public function payload(): array
    {
        return $this->payload;
    }

    public function occurredAt(): \DateTimeImmutable
    {
        return $this->occurredAt;
    }
}
