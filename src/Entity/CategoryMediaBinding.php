<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Entity;

use App\Cataloging\EntityInterface\CategoryMediaBindingInterface;
use App\Cataloging\ValueObject\CategoryMediaRole;
use App\Cataloging\ValueObjectInterface\CategoryMediaRoleInterface;

/**
 * Represents the category media binding domain record.
 */
final readonly class CategoryMediaBinding implements CategoryMediaBindingInterface
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
        private CategoryMediaRole $role,
        private array $channels,
        private array $locales,
        private bool $requiredForPublish,
        private bool $active,
        private array $metadata,
        private string $actorId,
        private \DateTimeImmutable $boundAt,
    ) {
    }

    /**
     * Handles the binding id workflow.
     */
    public function bindingId(): string
    {
        return $this->bindingId;
    }

    /**
     * Handles the category id workflow.
     */
    public function categoryId(): string
    {
        return $this->categoryId;
    }

    /**
     * Handles the asset id workflow.
     */
    public function assetId(): string
    {
        return $this->assetId;
    }

    /**
     * Handles the role workflow.
     */
    public function role(): CategoryMediaRoleInterface
    {
        return $this->role;
    }

    /**
     * Handles the channels workflow.
     */
    public function channels(): array
    {
        return $this->channels;
    }

    /**
     * Handles the locales workflow.
     */
    public function locales(): array
    {
        return $this->locales;
    }

    /**
     * Handles the required for publish workflow.
     */
    public function requiredForPublish(): bool
    {
        return $this->requiredForPublish;
    }

    /**
     * Handles the active workflow.
     */
    public function active(): bool
    {
        return $this->active;
    }

    /**
     * Handles the metadata workflow.
     */
    public function metadata(): array
    {
        return $this->metadata;
    }

    /**
     * Handles the actor id workflow.
     */
    public function actorId(): string
    {
        return $this->actorId;
    }

    /**
     * Handles the bound at workflow.
     */
    public function boundAt(): \DateTimeImmutable
    {
        return $this->boundAt;
    }
}
