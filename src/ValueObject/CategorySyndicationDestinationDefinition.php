<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ValueObject;

/**
 * Carries canonical identity and mode metadata for syndication destinations.
 */
final readonly class CategorySyndicationDestinationDefinition
{
    public function __construct(
        private string $destinationId,
        private string $name,
        private string $destinationType,
        private string $deliveryMode,
    ) {
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
}
