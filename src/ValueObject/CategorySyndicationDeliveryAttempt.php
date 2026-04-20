<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ValueObject;

/**
 * Carries response and retry attempt data for syndication delivery recording.
 */
final readonly class CategorySyndicationDeliveryAttempt
{
    /**
     * Initializes the category syndication delivery attempt value object.
     */
    public function __construct(
        private int $attempt,
        private ?int $responseCode,
        private string $responseMessage,
    ) {
    }

    public function attempt(): int
    {
        return $this->attempt;
    }

    public function responseCode(): ?int
    {
        return $this->responseCode;
    }

    public function responseMessage(): string
    {
        return $this->responseMessage;
    }
}
