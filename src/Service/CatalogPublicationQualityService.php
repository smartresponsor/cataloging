<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\Event\CategoryPublicationQualityEvaluated;
use App\EventInterface\CategoryPublicationQualityEvaluatedInterface;
use App\PolicyInterface\CategoryPublicationQualityPolicyInterface;
use App\ServiceInterface\CatalogPublicationQualityServiceInterface;

/**
 * Provides the catalog publication quality service application service.
 */
final readonly class CatalogPublicationQualityService implements CatalogPublicationQualityServiceInterface
{
    /**
     * Initializes the catalog publication quality service service collaborators.
     */
    public function __construct(private CategoryPublicationQualityPolicyInterface $policy)
    {
    }

    /**
     * Handles the evaluate workflow.
     */
    public function evaluate(
        string $categoryId,
        int $score,
        array $publicationChecks,
        array $checks,
        string $actorId,
        string $reason,
    ): CategoryPublicationQualityEvaluatedInterface {
        $profile = $this->policy->buildProfile($score, $publicationChecks, $checks);

        return new CategoryPublicationQualityEvaluated(
            trim($categoryId),
            $profile->score(),
            $profile->isPublishableQuality(),
            $profile->riskLevel(),
            $profile->hardBlockers(),
            $profile->softWarnings(),
            $profile->advisoryWarnings(),
            $profile->publicationChecks(),
            $profile->checks(),
            trim($actorId),
            trim($reason),
            new \DateTimeImmutable('now'),
        );
    }
}
