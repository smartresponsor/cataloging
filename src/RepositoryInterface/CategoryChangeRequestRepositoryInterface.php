<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\RepositoryInterface;

use App\EntityInterface\CategoryChangeRequestInterface;
use App\EventInterface\CategoryChangeRequestReviewedInterface;

interface CategoryChangeRequestRepositoryInterface
{
    public function findByRequestId(string $requestId): ?CategoryChangeRequestInterface;

    /** @return list<CategoryChangeRequestInterface> */
    public function findByCategoryId(string $categoryId): array;

    public function save(CategoryChangeRequestInterface $request): void;

    public function appendReviewHistory(CategoryChangeRequestReviewedInterface $event): void;

    /** @return list<CategoryChangeRequestReviewedInterface> */
    public function reviewHistoryForRequestId(string $requestId): array;
}
