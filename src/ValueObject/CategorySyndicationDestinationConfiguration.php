<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ValueObject;

/**
 * Carries activation and settings state for syndication destinations.
 */
final readonly class CategorySyndicationDestinationConfiguration
{
    /**
     * @param array<string,mixed> $settings
     */
    public function __construct(
        private bool $enabled,
        private array $settings,
    ) {
    }

    public function enabled(): bool
    {
        return $this->enabled;
    }

    /** @return array<string,mixed> */
    public function settings(): array
    {
        return $this->settings;
    }
}
