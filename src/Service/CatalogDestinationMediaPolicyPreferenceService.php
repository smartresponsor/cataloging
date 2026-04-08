<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\Event\CategoryDestinationMediaPolicyPreferenceEvaluated;
use App\EventInterface\CategoryDestinationMediaPolicyPreferenceEvaluatedInterface;
use App\PolicyInterface\CategoryDestinationMediaPolicyPreferencePolicyInterface;
use App\RepositoryInterface\CategorySyndicationDestinationRepositoryInterface;
use App\ServiceInterface\CatalogDestinationMediaFallbackServiceInterface;
use App\ServiceInterface\CatalogDestinationMediaPolicyPreferenceServiceInterface;
use App\ServiceInterface\CatalogDestinationMediaReadinessServiceInterface;
/**
 * Provides the catalog destination media policy preference service application service.
 */
final class CatalogDestinationMediaPolicyPreferenceService implements CatalogDestinationMediaPolicyPreferenceServiceInterface
{
    /**
     * Initializes the catalog destination media policy preference service service collaborators.
     */
    public function __construct(
        private readonly CategorySyndicationDestinationRepositoryInterface $destinationRepository,
        private readonly CatalogDestinationMediaReadinessServiceInterface $destinationMediaReadinessService,
        private readonly CatalogDestinationMediaFallbackServiceInterface $destinationMediaFallbackService,
        private readonly CategoryDestinationMediaPolicyPreferencePolicyInterface $policy,
    ) {
    }
    /**
     * Handles the evaluate workflow.
     */
    public function evaluate(string $destinationId, string $categoryId, string $actorId, string $reason): CategoryDestinationMediaPolicyPreferenceEvaluatedInterface
    {
        $destination = $this->destinationRepository->find($destinationId);
        if (null === $destination) {
            throw new \InvalidArgumentException('Unknown destination.');
        }

        $settings = $destination->settings();
        $mode = trim((string) ($settings['mediaPolicyMode'] ?? 'allow_fallback'));

        $strictPayload = $this->destinationMediaReadinessService->evaluate($destinationId, $categoryId, $actorId, $reason)->payload();
        $fallbackPayload = $this->destinationMediaFallbackService->evaluate($destinationId, $categoryId, $actorId, $reason)->payload();
        $report = $this->policy->buildReport($mode, $strictPayload, $fallbackPayload);

        return new CategoryDestinationMediaPolicyPreferenceEvaluated(
            [
                'destinationId' => trim($destinationId),
                'categoryId' => trim($categoryId),
                'mediaPolicyMode' => $report->mediaPolicyMode(),
                'strictPublishable' => $report->strictPublishable(),
                'fallbackPublishable' => $report->fallbackPublishable(),
                'resolvedPublishable' => $report->resolvedPublishable(),
                'fallbackUsed' => $report->fallbackUsed(),
                'requiredMissing' => $report->requiredMissing(),
                'warnings' => $report->warnings(),
                'checks' => $report->checks(),
                'channel' => trim((string) ($settings['channel'] ?? '')),
                'locale' => trim((string) ($settings['locale'] ?? '')),
                'actorId' => trim($actorId),
                'reason' => trim($reason),
            ],
            new \DateTimeImmutable('now'),
        );
    }
}
