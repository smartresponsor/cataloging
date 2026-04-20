<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ValueObject;

use App\Cataloging\ValueObjectInterface\CategorySyndicationRecoveryCandidateInterface;

/**
 * Represents the category syndication recovery candidate value.
 */
final readonly class CategorySyndicationRecoveryCandidate implements CategorySyndicationRecoveryCandidateInterface
{
    /**
     * Initializes the category syndication recovery candidate service collaborators.
     */
    public function __construct(
        private string $deliveryId,
        private string $packageId,
        private string $destinationId,
        private string $categoryId,
        private int $attempt,
        private ?int $responseCode,
        private string $responseMessage,
        private bool $retryable,
    ) {
    }

    /**
     * Handles the delivery id workflow.
     */
    public function deliveryId(): string
    {
        return $this->deliveryId;
    }

    /**
     * Handles the package id workflow.
     */
    public function packageId(): string
    {
        return $this->packageId;
    }

    /**
     * Handles the destination id workflow.
     */
    public function destinationId(): string
    {
        return $this->destinationId;
    }

    /**
     * Handles the category id workflow.
     */
    public function categoryId(): string
    {
        return $this->categoryId;
    }

    /**
     * Handles the attempt workflow.
     */
    public function attempt(): int
    {
        return $this->attempt;
    }

    /**
     * Handles the response code workflow.
     */
    public function responseCode(): ?int
    {
        return $this->responseCode;
    }

    /**
     * Handles the response message workflow.
     */
    public function responseMessage(): string
    {
        return $this->responseMessage;
    }

    /**
     * Handles the retryable workflow.
     */
    public function retryable(): bool
    {
        return $this->retryable;
    }
}
