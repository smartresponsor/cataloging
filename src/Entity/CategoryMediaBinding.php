<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Entity;

use App\EntityInterface\CategoryMediaBindingInterface;
use App\ValueObject\CategoryMediaRole;
use App\ValueObjectInterface\CategoryMediaRoleInterface;

final class CategoryMediaBinding implements CategoryMediaBindingInterface
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
        private readonly CategoryMediaRole $role,
        private readonly array $channels,
        private readonly array $locales,
        private readonly bool $requiredForPublish,
        private readonly bool $active,
        private readonly array $metadata,
        private readonly string $actorId,
        private readonly \DateTimeImmutable $boundAt,
    ) {
    }

    public function bindingId(): string
    {
        return $this->bindingId;
    }

    public function categoryId(): string
    {
        return $this->categoryId;
    }

    public function assetId(): string
    {
        return $this->assetId;
    }

    public function role(): CategoryMediaRoleInterface
    {
        return $this->role;
    }

    public function channels(): array
    {
        return $this->channels;
    }

    public function locales(): array
    {
        return $this->locales;
    }

    public function requiredForPublish(): bool
    {
        return $this->requiredForPublish;
    }

    public function active(): bool
    {
        return $this->active;
    }

    public function metadata(): array
    {
        return $this->metadata;
    }

    public function actorId(): string
    {
        return $this->actorId;
    }

    public function boundAt(): \DateTimeImmutable
    {
        return $this->boundAt;
    }
}
