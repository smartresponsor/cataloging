<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Service;

use App\Event\CategoryDestinationMediaFallbackEvaluated;
use App\EventInterface\CategoryDestinationMediaFallbackEvaluatedInterface;
use App\PolicyInterface\CategoryDestinationMediaFallbackPolicyInterface;
use App\RepositoryInterface\CategoryMediaBindingRepositoryInterface;
use App\RepositoryInterface\CategorySyndicationDestinationRepositoryInterface;
use App\ServiceInterface\CategoryDestinationMediaFallbackServiceInterface;

final class CategoryDestinationMediaFallbackService implements CategoryDestinationMediaFallbackServiceInterface
{
    public function __construct(
        private readonly CategorySyndicationDestinationRepositoryInterface $destinationRepository,
        private readonly CategoryMediaBindingRepositoryInterface $bindingRepository,
        private readonly CategoryDestinationMediaFallbackPolicyInterface $policy,
    ) {
    }

    public function evaluate(string $destinationId, string $categoryId, string $actorId, string $reason): CategoryDestinationMediaFallbackEvaluatedInterface
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
