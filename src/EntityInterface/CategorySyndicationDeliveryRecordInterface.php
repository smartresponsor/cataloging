<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\EntityInterface;

use App\ValueObjectInterface\CategorySyndicationDeliveryStatusInterface;

interface CategorySyndicationDeliveryRecordInterface
{
    public function deliveryId(): string;

    public function packageId(): string;

    public function destinationId(): string;

    public function categoryId(): string;

    public function status(): CategorySyndicationDeliveryStatusInterface;

    public function attempt(): int;

    public function responseCode(): ?int;

    public function responseMessage(): string;

    public function deliveredAt(): ?\DateTimeImmutable;
}
