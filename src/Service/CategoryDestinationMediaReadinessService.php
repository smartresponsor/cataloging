<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Service;

use App\Event\CategoryDestinationMediaReadinessEvaluated;
use App\EventInterface\CategoryDestinationMediaReadinessEvaluatedInterface;
use App\PolicyInterface\CategoryDestinationMediaReadinessPolicyInterface;
use App\RepositoryInterface\CategorySyndicationDestinationRepositoryInterface;
use App\ServiceInterface\CategoryDestinationMediaReadinessServiceInterface;
use App\ServiceInterface\CategoryMediaApplicabilityServiceInterface;

final class CategoryDestinationMediaReadinessService implements CategoryDestinationMediaReadinessServiceInterface
{
    public function __construct(
        private readonly CategorySyndicationDestinationRepositoryInterface $destinationRepository,
        private readonly CategoryMediaApplicabilityServiceInterface $applicabilityService,
        private readonly CategoryDestinationMediaReadinessPolicyInterface $policy,
    ) {
    }

    public function evaluate(string $destinationId, string $categoryId, string $actorId, string $reason): CategoryDestinationMediaReadinessEvaluatedInterface
    {
        $destination = $this->destinationRepository->find($destinationId);
        if (null === $destination) {
            throw new \InvalidArgumentException('Unknown destination.');
        }

        $settings = $destination->settings();
        $payload = [
            'channel' => trim((string) ($settings['channel'] ?? '')),
            'locale' => trim((string) ($settings['locale'] ?? '')),
            'requiredRoles' => is_array($settings['requiredMediaRoles'] ?? null) ? $settings['requiredMediaRoles'] : [],
        ];

        $applicability = $this->applicabilityService->evaluate($categoryId, $payload, $actorId, $reason);
        $applicabilityPayload = $applicability->payload();

        $report = $this->policy->buildReport(
            $destinationId,
            $categoryId,
            $settings,
            $payload,
            is_array($applicabilityPayload['checks'] ?? null) ? $applicabilityPayload['checks'] : [],
            is_array($applicabilityPayload['requiredMissing'] ?? null) ? $applicabilityPayload['requiredMissing'] : [],
            is_array($applicabilityPayload['warnings'] ?? null) ? $applicabilityPayload['warnings'] : [],
            is_array($applicabilityPayload['matchedBindingIds'] ?? null) ? $applicabilityPayload['matchedBindingIds'] : [],
        );

        return new CategoryDestinationMediaReadinessEvaluated(
            trim($destinationId),
            trim($categoryId),
            trim((string) ($settings['channel'] ?? '')),
            trim((string) ($settings['locale'] ?? '')),
            $report->publishable(),
            $report->requiredMissing(),
            $report->warnings(),
            $report->checks(),
            $report->matchedBindingIds(),
            trim($actorId),
            trim($reason),
            new \DateTimeImmutable(),
        );
    }
}
