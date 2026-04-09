<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Event;

use App\EventInterface\CategoryMediaBoundInterface;

/**
 * Represents the category media bound application event.
 */
final readonly class CategoryMediaBound implements CategoryMediaBoundInterface
{
    /**
     * @param list<string>        $channels
     * @param list<string>        $locales
     * @param array<string,mixed> $metadata
     */
    public function __construct(
        private string $bindingId,
        private string $categoryId,
        private string $assetId,
        private string $role,
        private array $channels,
        private array $locales,
        private bool $requiredForPublish,
        private bool $active,
        private array $metadata,
        private string $actorId,
        private string $reason,
        private \DateTimeImmutable $boundAt,
    ) {
    }

    /**
     * Handles the payload workflow.
     */
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
