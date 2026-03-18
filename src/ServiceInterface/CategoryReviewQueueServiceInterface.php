<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\ServiceInterface;

use App\ValueObjectInterface\CategoryReviewQueueItemInterface;

interface CategoryReviewQueueServiceInterface
{
    /** @return list<CategoryReviewQueueItemInterface> */
    public function queueForReviewer(string $reviewer): array;
}
