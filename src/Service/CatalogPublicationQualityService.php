<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\Event\CategoryPublicationQualityEvaluated;
use App\Cataloging\EventInterface\CategoryPublicationQualityEvaluatedInterface;
use App\Cataloging\PolicyInterface\CategoryPublicationQualityPolicyInterface;
use App\Cataloging\ServiceInterface\CatalogPublicationQualityServiceInterface;

final class CatalogPublicationQualityService implements CatalogPublicationQualityServiceInterface
{
    public function __construct(private readonly CategoryPublicationQualityPolicyInterface $policy)
    {
    }

    public function evaluate(string $categoryId, int $score, array $publicationChecks, array $checks, string $actorId, string $reason): CategoryPublicationQualityEvaluatedInterface
    {
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
