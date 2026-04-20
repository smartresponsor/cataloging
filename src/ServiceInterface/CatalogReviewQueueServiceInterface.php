<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ServiceInterface;

use App\Cataloging\ValueObject\CategoryReviewQueueRequest;
use App\Cataloging\ValueObjectInterface\CategoryReviewQueueItemInterface;

/**
 * Defines the contract for catalog review queue service.
 */
interface CatalogReviewQueueServiceInterface
{
    /** @return list<CategoryReviewQueueItemInterface> */
    public function queueForReviewer(CategoryReviewQueueRequest $request): array;
}
