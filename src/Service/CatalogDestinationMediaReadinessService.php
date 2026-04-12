<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\Event\CategoryDestinationMediaReadinessEvaluated;
use App\EventInterface\CategoryDestinationMediaReadinessEvaluatedInterface;
use App\PolicyInterface\CategoryDestinationMediaReadinessPolicyInterface;
use App\RepositoryInterface\CategorySyndicationDestinationRepositoryInterface;
use App\ServiceInterface\CatalogDestinationMediaReadinessServiceInterface;
use App\ServiceInterface\CatalogMediaApplicabilityServiceInterface;
use App\ValueObject\CategoryDestinationMediaEvaluationRequest;
use App\ValueObject\CategoryDestinationMediaReadinessContext;
use App\ValueObject\CategoryDestinationMediaReadinessState;
use App\ValueObject\CategoryEvaluationRequest;

/**
 * Provides the catalog destination media readiness service application service.
 */
final readonly class CatalogDestinationMediaReadinessService implements CatalogDestinationMediaReadinessServiceInterface
{
    /**
     * Initializes the catalog destination media readiness service service collaborators.
     */
    public function __construct(
        private CategorySyndicationDestinationRepositoryInterface $destinationRepository,
        private CatalogMediaApplicabilityServiceInterface $applicabilityService,
        private CategoryDestinationMediaReadinessPolicyInterface $policy,
    ) {
    }

    /**
     * Handles the evaluate workflow.
     */
    public function evaluate(
        CategoryDestinationMediaEvaluationRequest $request,
    ): CategoryDestinationMediaReadinessEvaluatedInterface {
        $destinationId = $request->destinationId();
        $categoryId = $request->categoryId();
        $destination = $this->destinationRepository->find($destinationId);
        if (null === $destination) {
            throw new \InvalidArgumentException('Unknown destination.');
        }
        $settings = $destination->settings();
        $payload = CategoryMediaInputNormalizer::destinationPayload($settings);
        $applicability = $this->applicabilityService->evaluate(
            new CategoryEvaluationRequest(
                $categoryId,
                $payload,
                $request->auditContext(),
            ),
        );
        $applicabilityPayload = $applicability->payload();

        $report = $this->policy->buildReport(
            new CategoryDestinationMediaReadinessContext(
                $destinationId,
                $categoryId,
                $settings,
                $payload,
            ),
            new CategoryDestinationMediaReadinessState(
                CategoryMediaInputNormalizer::boolMap($applicabilityPayload['checks'] ?? null),
                CategoryMediaInputNormalizer::stringList($applicabilityPayload['requiredMissing'] ?? null),
                CategoryMediaInputNormalizer::stringList($applicabilityPayload['warnings'] ?? null),
                CategoryMediaInputNormalizer::stringList($applicabilityPayload['matchedBindingIds'] ?? null),
            ),
        );

        return new CategoryDestinationMediaReadinessEvaluated(
            $destinationId,
            $categoryId,
            CategoryMediaInputNormalizer::stringValue($settings['channel'] ?? null),
            CategoryMediaInputNormalizer::stringValue($settings['locale'] ?? null),
            $report->publishable(),
            $report->requiredMissing(),
            $report->warnings(),
            $report->checks(),
            $report->matchedBindingIds(),
            $request->actorId(),
            $request->reason(),
            new \DateTimeImmutable(),
        );
    }
}
