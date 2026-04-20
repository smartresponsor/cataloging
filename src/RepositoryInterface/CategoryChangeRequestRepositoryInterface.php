<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\RepositoryInterface;

use App\Cataloging\EntityInterface\CategoryChangeRequestInterface;
use App\Cataloging\EventInterface\CategoryChangeRequestReviewedInterface;

/**
 * Defines the contract for category change request repository.
 */
interface CategoryChangeRequestRepositoryInterface
{
    /**
     * Handles the find by request id workflow.
     */
    public function findByRequestId(string $requestId): ?CategoryChangeRequestInterface;

    /** @return list<CategoryChangeRequestInterface> */
    public function findByCategoryId(string $categoryId): array;

    /**
     * Handles the save workflow.
     */
    public function save(CategoryChangeRequestInterface $request): void;

    /**
     * Handles the append review history workflow.
     */
    public function appendReviewHistory(CategoryChangeRequestReviewedInterface $event): void;

    /** @return list<CategoryChangeRequestReviewedInterface> */
    public function reviewHistoryForRequestId(string $requestId): array;
}
