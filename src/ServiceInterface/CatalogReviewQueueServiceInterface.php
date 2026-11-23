<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ServiceInterface;

use App\Cataloging\ValueObjectInterface\CategoryReviewQueueItemInterface;

interface CatalogReviewQueueServiceInterface
{
    /** @return list<CategoryReviewQueueItemInterface> */
    public function queueForReviewer(string $reviewer): array;
}
