<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Entity;

use App\EntityInterface\CategorySyndicationDeliveryRecordInterface;
use App\ValueObjectInterface\CategorySyndicationDeliveryStatusInterface;

final class CategorySyndicationDeliveryRecord implements CategorySyndicationDeliveryRecordInterface
{
    public function __construct(
        private readonly string $deliveryId,
        private readonly string $packageId,
        private readonly string $destinationId,
        private readonly string $categoryId,
        private readonly CategorySyndicationDeliveryStatusInterface $status,
        private readonly int $attempt,
        private readonly ?int $responseCode,
        private readonly string $responseMessage,
        private readonly ?\DateTimeImmutable $deliveredAt,
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

    public function status(): CategorySyndicationDeliveryStatusInterface
    {
        return $this->status;
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

    public function deliveredAt(): ?\DateTimeImmutable
    {
        return $this->deliveredAt;
    }
}
