<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\ServiceInterface;

use App\Entity\CategoryChangeRequest;
use App\Event\CategoryChangeRequestReviewed;

interface CategoryChangeRequestServiceInterface
{
    /** @param array<string,mixed> $changes */
    public function submit(string $requestId, string $categoryId, string $submittedBy, string $summary, array $changes): CategoryChangeRequest;

    public function review(string $requestId, string $targetState, string $reviewedBy, string $decisionReason): CategoryChangeRequestReviewed;
}
