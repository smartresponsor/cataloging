<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObject;

/**
 * Carries destination/category context for media readiness evaluations.
 */
final readonly class CategoryDestinationMediaReadinessContext
{
    /**
     * @param array<string,mixed> $destinationSettings
     * @param array<string,mixed> $applicabilityPayload
     */
    public function __construct(
        private string $destinationId,
        private string $categoryId,
        private array $destinationSettings,
        private array $applicabilityPayload,
    ) {
    }

    public function destinationId(): string
    {
        return $this->destinationId;
    }

    public function categoryId(): string
    {
        return $this->categoryId;
    }

    /** @return array<string,mixed> */
    public function destinationSettings(): array
    {
        return $this->destinationSettings;
    }

    /** @return array<string,mixed> */
    public function applicabilityPayload(): array
    {
        return $this->applicabilityPayload;
    }
}
