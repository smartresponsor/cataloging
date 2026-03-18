<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\RepositoryInterface;

use App\EntityInterface\CategoryChangeRequestInterface;
use App\EventInterface\CategoryChangeRequestReviewedInterface;

interface CategoryChangeRequestRepositoryInterface
{
    public function findByRequestId(string $requestId): ?CategoryChangeRequestInterface;

    public function save(CategoryChangeRequestInterface $request): void;

    public function appendReviewHistory(CategoryChangeRequestReviewedInterface $event): void;

    /** @return list<CategoryChangeRequestReviewedInterface> */
    public function reviewHistoryForRequestId(string $requestId): array;
}
