<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Entity;

use App\EntityInterface\CategorySyndicationDestinationInterface;

final class CategorySyndicationDestination implements CategorySyndicationDestinationInterface
{
    /**
     * @param array<string,string> $settings
     */
    public function __construct(
        private readonly string $destinationId,
        private readonly string $name,
        private readonly string $destinationType,
        private readonly string $deliveryMode,
        private readonly bool $enabled,
        private readonly array $settings,
        private readonly string $createdBy,
        private readonly \DateTimeImmutable $createdAt,
    ) {
    }

    /**
     * @param array<string,string> $settings
     */
    public static function register(
        string $destinationId,
        string $name,
        string $destinationType,
        string $deliveryMode,
        bool $enabled,
        array $settings,
        string $createdBy,
    ): self {
        return new self(
            trim($destinationId),
            trim($name),
            trim($destinationType),
            trim($deliveryMode),
            $enabled,
            $settings,
            trim($createdBy),
            new \DateTimeImmutable('now'),
        );
    }

    public function destinationId(): string
    {
        return $this->destinationId;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function destinationType(): string
    {
        return $this->destinationType;
    }

    public function deliveryMode(): string
    {
        return $this->deliveryMode;
    }

    public function enabled(): bool
    {
        return $this->enabled;
    }

    public function settings(): array
    {
        return $this->settings;
    }

    public function createdBy(): string
    {
        return $this->createdBy;
    }

    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
