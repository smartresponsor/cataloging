<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObject;

use App\ValueObjectInterface\CategorySyndicationRecoveryCandidateInterface;

final class CategorySyndicationRecoveryCandidate implements CategorySyndicationRecoveryCandidateInterface
{
    public function __construct(
        private readonly string $deliveryId,
        private readonly string $packageId,
        private readonly string $destinationId,
        private readonly string $categoryId,
        private readonly int $attempt,
        private readonly ?int $responseCode,
        private readonly string $responseMessage,
        private readonly bool $retryable,
    ) {
    }

    public function deliveryId(): string
    {
        return $this->deliveryId;
    }

    public function packageId(): string
    {
        return $this->packageId;
    }

    public function destinationId(): string
    {
        return $this->destinationId;
    }

    public function categoryId(): string
    {
        return $this->categoryId;
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

    public function retryable(): bool
    {
        return $this->retryable;
    }
}
