<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\Event\CategoryDestinationMediaFallbackEvaluated;
use App\EventInterface\CategoryDestinationMediaFallbackEvaluatedInterface;
use App\PolicyInterface\CategoryDestinationMediaFallbackPolicyInterface;
use App\RepositoryInterface\CategoryMediaBindingRepositoryInterface;
use App\RepositoryInterface\CategorySyndicationDestinationRepositoryInterface;
use App\ServiceInterface\CatalogDestinationMediaFallbackServiceInterface;
/**
 * Provides the catalog destination media fallback service application service.
 */
final class CatalogDestinationMediaFallbackService implements CatalogDestinationMediaFallbackServiceInterface
{
    /**
     * Initializes the catalog destination media fallback service service collaborators.
     */
    public function __construct(
        private readonly CategorySyndicationDestinationRepositoryInterface $destinationRepository,
        private readonly CategoryMediaBindingRepositoryInterface $bindingRepository,
        private readonly CategoryDestinationMediaFallbackPolicyInterface $policy,
    ) {
    }
    /**
     * Handles the evaluate workflow.
     */
    public function evaluate(
        string $destinationId,
        string $categoryId,
        string $actorId,
        string $reason,
    ): CategoryDestinationMediaFallbackEvaluatedInterface
    {
        $destination = $this->destinationRepository->find($destinationId);
        if (null === $destination) {
            throw new \InvalidArgumentException('Unknown destination.');
        }

        $settings = $destination->settings();
        $report = $this->policy->buildReport(
            $destinationId,
            $categoryId,
            $settings,
            $this->bindingRepository->bindingsForCategory($categoryId),
        );

        return new CategoryDestinationMediaFallbackEvaluated(
            trim($destinationId),
            trim($categoryId),
            trim((string) ($settings['channel'] ?? '')),
            trim((string) ($settings['locale'] ?? '')),
            $report->publishable(),
            $report->publishableWithFallback(),
            $report->requiredMissing(),
            $report->warnings(),
            $report->checks(),
            $report->exactMatchedBindingIds(),
            $report->fallbackMatchedBindingIds(),
            trim($actorId),
            trim($reason),
            new \DateTimeImmutable(),
        );
    }
}
