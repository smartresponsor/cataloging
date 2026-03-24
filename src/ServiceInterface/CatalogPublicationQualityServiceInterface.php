<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\ServiceInterface;

use App\EventInterface\CategoryPublicationQualityEvaluatedInterface;

interface CatalogPublicationQualityServiceInterface
{
    /**
     * @param array<string,bool> $publicationChecks
     * @param array<string,bool> $checks
     */
    public function evaluate(string $categoryId, int $score, array $publicationChecks, array $checks, string $actorId, string $reason): CategoryPublicationQualityEvaluatedInterface;
}
