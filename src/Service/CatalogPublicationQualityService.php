<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\Event\Catalog\CatalogCategoryPublicationQualityEvaluatedEvent;
use App\Cataloging\EventInterface\Catalog\CatalogCategoryPublicationQualityEvaluatedEventInterface;
use App\Cataloging\PolicyInterface\CategoryPublicationQualityPolicyInterface;
use App\Cataloging\ServiceInterface\CatalogPublicationQualityServiceInterface;
use App\Cataloging\ValueObject\CategoryPublicationQualityEvaluationRequest;

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
    ): CatalogCategoryPublicationQualityEvaluatedEventInterface {
        $input = $request->input();
        $audit = $request->auditContext();
        $profile = $this->policy->buildProfile($input->score(), $input->publicationChecks(), $input->checks());

        return new CatalogCategoryPublicationQualityEvaluatedEvent(
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
