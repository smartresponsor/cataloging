<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

use App\Entity\CatalogCategoryChangeRequest;
use App\Event\CategoryChangeRequestReviewed;
use App\ValueObject\CategoryChangeRequestReviewRequest;
use App\ValueObject\CategoryChangeRequestSubmitRequest;

/**
 * Defines the contract for catalog change request service.
 */
interface CatalogChangeRequestServiceInterface
{
    public function submit(CategoryChangeRequestSubmitRequest $request): CatalogCategoryChangeRequest;

    /**
     * Handles the review workflow.
     */
    public function review(CategoryChangeRequestReviewRequest $request): CategoryChangeRequestReviewed;
}
