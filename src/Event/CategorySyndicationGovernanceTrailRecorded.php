<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Event;

use App\Cataloging\EventInterface\CategorySyndicationGovernanceTrailRecordedInterface;

final class CategorySyndicationGovernanceTrailRecorded implements CategorySyndicationGovernanceTrailRecordedInterface
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
