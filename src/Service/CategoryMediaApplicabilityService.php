<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Service;

use App\Event\CategoryMediaApplicabilityEvaluated;
use App\EventInterface\CategoryMediaApplicabilityEvaluatedInterface;
use App\PolicyInterface\CategoryMediaApplicabilityPolicyInterface;
use App\RepositoryInterface\CategoryMediaBindingRepositoryInterface;
use App\ServiceInterface\CategoryMediaApplicabilityServiceInterface;

final class CategoryMediaApplicabilityService implements CategoryMediaApplicabilityServiceInterface
{
    public function __construct(
        private readonly CategoryMediaBindingRepositoryInterface $repository,
        private readonly CategoryMediaApplicabilityPolicyInterface $policy,
    ) {
    }

    public function evaluate(string $categoryId, array $payload, string $actorId, string $reason): CategoryMediaApplicabilityEvaluatedInterface
    {
        $report = $this->policy->buildReport($payload, $this->repository->bindingsForCategory($categoryId));

        return new CategoryMediaApplicabilityEvaluated(
            trim($categoryId),
            trim((string) ($payload['channel'] ?? '')),
            trim((string) ($payload['locale'] ?? '')),
            $report->requiredMissing(),
            $report->warnings(),
            $report->checks(),
            $report->matchedBindingIds(),
            trim($actorId),
            trim($reason),
            new \DateTimeImmutable('now'),
        );
    }
}
