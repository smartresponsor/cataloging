<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\Event\CategoryPublicationQualityEvaluated;
use App\EventInterface\CategoryPublicationQualityEvaluatedInterface;
use App\PolicyInterface\CategoryPublicationQualityPolicyInterface;
use App\ServiceInterface\CatalogPublicationQualityServiceInterface;
use App\ValueObject\CategoryPublicationQualityEvaluationRequest;

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
        CategoryPublicationQualityEvaluationRequest $request,
    ): CategoryPublicationQualityEvaluatedInterface {
        $input = $request->input();
        $audit = $request->auditContext();
        $profile = $this->policy->buildProfile($input->score(), $input->publicationChecks(), $input->checks());

        return new CategoryPublicationQualityEvaluated(
            trim($input->categoryId()),
            $profile->score(),
            $profile->isPublishableQuality(),
            $profile->riskLevel(),
            $profile->hardBlockers(),
            $profile->softWarnings(),
            $profile->advisoryWarnings(),
            $profile->publicationChecks(),
            $profile->checks(),
            trim($audit->actorId()),
            trim($audit->reason()),
            new \DateTimeImmutable('now'),
        );
    }
}
