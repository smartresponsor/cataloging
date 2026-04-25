<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\Event\CategoryDestinationMediaFallbackEvaluated;
use App\Cataloging\EventInterface\CategoryDestinationMediaFallbackEvaluatedInterface;
use App\Cataloging\PolicyInterface\CategoryDestinationMediaFallbackPolicyInterface;
use App\Cataloging\RepositoryInterface\CatalogCategoryMediaBindingEntityRepositoryInterface;
use App\Cataloging\RepositoryInterface\CatalogSyndicationDestinationRepositoryInterface;
use App\Cataloging\ServiceInterface\CatalogDestinationMediaFallbackServiceInterface;
use App\Cataloging\ValueObject\CategoryDestinationMediaEvaluationRequest;

/**
 * Provides the catalog destination media fallback service application service.
 */
final readonly class CatalogDestinationMediaFallbackService implements CatalogDestinationMediaFallbackServiceInterface
{
    /**
     * Initializes the catalog destination media fallback service service collaborators.
     */
    public function __construct(
        private CatalogSyndicationDestinationRepositoryInterface $destinationRepository,
        private CatalogCategoryMediaBindingEntityRepositoryInterface $bindingRepository,
        private CategoryDestinationMediaFallbackPolicyInterface $policy,
    ) {
    }

    /**
     * Handles the evaluate workflow.
     */
    public function evaluate(
        CategoryDestinationMediaEvaluationRequest $request,
    ): CategoryDestinationMediaFallbackEvaluatedInterface {
        $destinationId = $request->destinationId();
        $categoryId = $request->categoryId();
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
            $destinationId,
            $categoryId,
            trim($settings['channel'] ?? ''),
            trim($settings['locale'] ?? ''),
            $report->publishable(),
            $report->publishableWithFallback(),
            $report->requiredMissing(),
            $report->warnings(),
            $report->checks(),
            $report->exactMatchedBindingIds(),
            $report->fallbackMatchedBindingIds(),
            $request->actorId(),
            $request->reason(),
            new \DateTimeImmutable(),
        );
    }
}
