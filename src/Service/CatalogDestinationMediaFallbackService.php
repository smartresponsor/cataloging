<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\Event\Catalog\CatalogCategoryDestinationMediaFallbackEvaluatedEvent;
use App\Cataloging\EventInterface\Catalog\CatalogCategoryDestinationMediaFallbackEvaluatedEventInterface;
use App\Cataloging\PolicyInterface\CategoryDestinationMediaFallbackPolicyInterface;
use App\Cataloging\RepositoryInterface\Catalog\CatalogCategoryMediaBindingRepositoryInterface;
use App\Cataloging\RepositoryInterface\Catalog\CatalogSyndicationDestinationRepositoryInterface;
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
        private CatalogCategoryMediaBindingRepositoryInterface $bindingRepository,
        private CategoryDestinationMediaFallbackPolicyInterface $policy,
    ) {
    }

    /**
     * Handles the evaluate workflow.
     */
    public function evaluate(
        CategoryDestinationMediaEvaluationRequest $request,
    ): CatalogCategoryDestinationMediaFallbackEvaluatedEventInterface {
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

        return new CatalogCategoryDestinationMediaFallbackEvaluatedEvent(
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
