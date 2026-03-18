<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Event;

use App\EventInterface\CategoryMediaBoundInterface;

final class CategoryMediaBound implements CategoryMediaBoundInterface
{
    /**
     * @param list<string>        $channels
     * @param list<string>        $locales
     * @param array<string,mixed> $metadata
     */
    public function __construct(
        private readonly string $bindingId,
        private readonly string $categoryId,
        private readonly string $assetId,
        private readonly string $role,
        private readonly array $channels,
        private readonly array $locales,
        private readonly bool $requiredForPublish,
        private readonly bool $active,
        private readonly array $metadata,
        private readonly string $actorId,
        private readonly string $reason,
        private readonly \DateTimeImmutable $boundAt,
    ) {
    }

    public function payload(): array
    {
        return [
            'bindingId' => $this->bindingId,
            'categoryId' => $this->categoryId,
            'assetId' => $this->assetId,
            'role' => $this->role,
            'channels' => $this->channels,
            'locales' => $this->locales,
            'requiredForPublish' => $this->requiredForPublish,
            'active' => $this->active,
            'metadata' => $this->metadata,
            'actorId' => $this->actorId,
            'reason' => $this->reason,
            'boundAt' => $this->boundAt->format(DATE_ATOM),
        ];
    }
}
