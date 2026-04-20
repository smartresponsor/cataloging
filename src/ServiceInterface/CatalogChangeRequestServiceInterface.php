<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\ServiceInterface;

use App\Cataloging\Entity\CatalogCategoryChangeRequest;
use App\Cataloging\Event\CategoryChangeRequestReviewed;
use App\Cataloging\ValueObject\CategoryChangeRequestReviewRequest;
use App\Cataloging\ValueObject\CategoryChangeRequestSubmitRequest;

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
