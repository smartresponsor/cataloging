<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\Event\CategorySyndicationFallbackAwarePackageGated;
use App\EventInterface\CategorySyndicationFallbackAwarePackageGatedInterface;
use App\PolicyInterface\CategorySyndicationFallbackAwarePackageGatePolicyInterface;
use App\ServiceInterface\CatalogDestinationMediaFallbackServiceInterface;
use App\ServiceInterface\CatalogDestinationMediaReadinessServiceInterface;
use App\ServiceInterface\CatalogSyndicationFallbackAwarePackageGateServiceInterface;
use App\ServiceInterface\CatalogSyndicationMappingServiceInterface;
use App\ValueObject\CategoryDestinationMediaEvaluationRequest;
use App\ValueObject\CategorySyndicationPackageBuildRequest;

/**
 * Provides the catalog syndication fallback aware package gate service application service.
 */
final readonly class CatalogSyndicationFallbackAwarePackageGateService implements CatalogSyndicationFallbackAwarePackageGateServiceInterface
{
    /**
     * Initializes the catalog syndication fallback aware package gate service service collaborators.
     */
    public function __construct(
        private CatalogSyndicationMappingServiceInterface $mappingService,
        private CatalogDestinationMediaReadinessServiceInterface $destinationMediaReadinessService,
        private CatalogDestinationMediaFallbackServiceInterface $destinationMediaFallbackService,
        private CategorySyndicationFallbackAwarePackageGatePolicyInterface $policy,
    ) {
    }

    /**
     * Builds the fallback aware gated package result for the current workflow.
     */
    public function buildGatedPublishPackage(
        CategorySyndicationPackageBuildRequest $request,
    ): CategorySyndicationFallbackAwarePackageGatedInterface {
        $context = $request->context();
        $audit = $request->auditContext();
        $packageBuilt = $this->mappingService->buildPublishPackage($request);
        $packagePayload = $packageBuilt->payload();
        $destinationRequest = new CategoryDestinationMediaEvaluationRequest(
            $context->destinationId(),
            $context->categoryId(),
            $audit,
        );

        $strictMedia = $this->destinationMediaReadinessService->evaluate($destinationRequest)->payload();
        $fallbackMedia = $this->destinationMediaFallbackService->evaluate($destinationRequest)->payload();

        $report = $this->policy->buildReport(
            is_array($packagePayload['missingRequiredFields'] ?? null) ? $packagePayload['missingRequiredFields'] : [],
            is_array($strictMedia['requiredMissing'] ?? null) ? $strictMedia['requiredMissing'] : [],
            is_array($fallbackMedia['requiredMissing'] ?? null) ? $fallbackMedia['requiredMissing'] : [],
            array_merge(
                is_array($strictMedia['warnings'] ?? null) ? $strictMedia['warnings'] : [],
                is_array($fallbackMedia['warnings'] ?? null) ? $fallbackMedia['warnings'] : [],
            ),
            is_array($strictMedia['checks'] ?? null) ? $strictMedia['checks'] : [],
            is_array($fallbackMedia['checks'] ?? null) ? $fallbackMedia['checks'] : [],
            is_array($fallbackMedia['exactMatchedBindingIds'] ?? null) ? $fallbackMedia['exactMatchedBindingIds'] : [],
            is_array($fallbackMedia['fallbackMatchedBindingIds'] ?? null)
                ? $fallbackMedia['fallbackMatchedBindingIds']
                : [],
        );

        return new CategorySyndicationFallbackAwarePackageGated(
            [
                'packageId' => trim($context->packageId()),
                'destinationId' => trim($context->destinationId()),
                'categoryId' => trim($context->categoryId()),
                'version' => trim($context->version()),
                'localeMode' => trim($context->localeMode()),
                'payload' => is_array($packagePayload['payload'] ?? null) ? $packagePayload['payload'] : [],
                'fieldMap' => is_array($packagePayload['fieldMap'] ?? null) ? $packagePayload['fieldMap'] : [],
                'requiredFields' => is_array($packagePayload['requiredFields'] ?? null)
                    ? $packagePayload['requiredFields']
                    : [],
                'packageMissingRequiredFields' => $report->packageMissingRequiredFields(),
                'requiredMissing' => array_values(
                    array_unique(
                        array_merge($report->strictMediaRequiredMissing(), $report->fallbackMediaRequiredMissing()),
                    ),
                ),
                'warnings' => $report->warnings(),
                'checks' => $report->checks(),
                'exactMatchedBindingIds' => $report->exactMatchedBindingIds(),
                'fallbackMatchedBindingIds' => $report->fallbackMatchedBindingIds(),
                'strictPublishable' => $report->strictPublishable(),
                'fallbackPublishable' => $report->fallbackPublishable(),
                'actorId' => trim($audit->actorId()),
                'reason' => trim($audit->reason()),
            ],
            new \DateTimeImmutable(),
        );
    }
}
