<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\Event\CategoryDestinationMediaFallbackEvaluated;
use App\Cataloging\EventInterface\CategoryDestinationMediaFallbackEvaluatedInterface;
use App\Cataloging\PolicyInterface\CategoryDestinationMediaFallbackPolicyInterface;
use App\Cataloging\RepositoryInterface\CategoryMediaBindingRepositoryInterface;
use App\Cataloging\RepositoryInterface\CategorySyndicationDestinationRepositoryInterface;
use App\Cataloging\ServiceInterface\CatalogDestinationMediaFallbackServiceInterface;

final class CatalogDestinationMediaFallbackService implements CatalogDestinationMediaFallbackServiceInterface
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
