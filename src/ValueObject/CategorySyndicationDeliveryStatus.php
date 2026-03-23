<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ValueObject;

use App\ValueObjectInterface\CategorySyndicationDeliveryStatusInterface;

final class CategorySyndicationDeliveryStatus implements CategorySyndicationDeliveryStatusInterface
{
    public function __construct(
        private readonly string $status,
    ) {
    }

    public function status(): string
    {
        return $this->status;
    }
}
