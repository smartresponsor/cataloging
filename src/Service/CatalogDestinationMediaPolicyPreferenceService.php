<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\Event\Catalog\CatalogCategoryDestinationMediaPolicyPreferenceEvaluatedEvent;
use App\Cataloging\EventInterface\Catalog\CatalogCategoryDestinationMediaPolicyPreferenceEvaluatedEventInterface;
use App\Cataloging\PolicyInterface\CategoryDestinationMediaPolicyPreferencePolicyInterface;
use App\Cataloging\RepositoryInterface\Catalog\CatalogSyndicationDestinationRepositoryInterface;
use App\Cataloging\ServiceInterface\CatalogDestinationMediaFallbackServiceInterface;
use App\Cataloging\ServiceInterface\CatalogDestinationMediaPolicyPreferenceServiceInterface;
use App\Cataloging\ServiceInterface\CatalogDestinationMediaReadinessServiceInterface;
use App\Cataloging\ValueObject\CategoryDestinationMediaEvaluationRequest;

/**
 * Provides the catalog destination media policy preference service application service.
 */
final readonly class CatalogDestinationMediaPolicyPreferenceService implements CatalogDestinationMediaPolicyPreferenceServiceInterface
{
    /**
     * Initializes the catalog destination media policy preference service service collaborators.
     */
    public function __construct(
        private CatalogSyndicationDestinationRepositoryInterface $destinationRepository,
        private CatalogDestinationMediaReadinessServiceInterface $destinationMediaReadinessService,
        private CatalogDestinationMediaFallbackServiceInterface $destinationMediaFallbackService,
        private CategoryDestinationMediaPolicyPreferencePolicyInterface $policy,
    ) {
    }

    /**
     * Handles the evaluate workflow.
     */
    public function evaluate(
        CategoryDestinationMediaEvaluationRequest $request,
    ): CatalogCategoryDestinationMediaPolicyPreferenceEvaluatedEventInterface {
        $destinationId = $request->destinationId();
        $categoryId = $request->categoryId();
        $destination = $this->destinationRepository->find($destinationId);
        if (null === $destination) {
            throw new \InvalidArgumentException('Unknown destination.');
        }

        $settings = $destination->settings();
        $mode = trim($settings['mediaPolicyMode'] ?? 'allow_fallback');

        $strictPayload = $this->destinationMediaReadinessService->evaluate($request)->payload();
        $fallbackPayload = $this->destinationMediaFallbackService->evaluate($request)->payload();
        $report = $this->policy->buildReport($mode, $strictPayload, $fallbackPayload);

        return new CatalogCategoryDestinationMediaPolicyPreferenceEvaluatedEvent(
            [
                'destinationId' => $destinationId,
                'categoryId' => $categoryId,
                'mediaPolicyMode' => $report->mediaPolicyMode(),
                'strictPublishable' => $report->strictPublishable(),
                'fallbackPublishable' => $report->fallbackPublishable(),
                'resolvedPublishable' => $report->resolvedPublishable(),
                'fallbackUsed' => $report->fallbackUsed(),
                'requiredMissing' => $report->requiredMissing(),
                'warnings' => $report->warnings(),
                'checks' => $report->checks(),
                'channel' => trim($settings['channel'] ?? ''),
                'locale' => trim($settings['locale'] ?? ''),
                'actorId' => $request->actorId(),
                'reason' => $request->reason(),
            ],
            new \DateTimeImmutable('now'),
        );
    }
}
