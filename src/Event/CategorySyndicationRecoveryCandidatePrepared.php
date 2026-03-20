<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Event;

use App\EventInterface\CategorySyndicationRecoveryCandidatePreparedInterface;

final class CategorySyndicationRecoveryCandidatePrepared implements CategorySyndicationRecoveryCandidatePreparedInterface
{
    /**
     * @param array<string,mixed> $payload
     */
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
