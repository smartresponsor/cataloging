<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\Event\CategorySyndicationFallbackAwarePackageGated;
use App\Cataloging\EventInterface\CategorySyndicationFallbackAwarePackageGatedInterface;
use App\Cataloging\PolicyInterface\CategorySyndicationFallbackAwarePackageGatePolicyInterface;
use App\Cataloging\ServiceInterface\CatalogDestinationMediaFallbackServiceInterface;
use App\Cataloging\ServiceInterface\CatalogDestinationMediaReadinessServiceInterface;
use App\Cataloging\ServiceInterface\CatalogSyndicationFallbackAwarePackageGateServiceInterface;
use App\Cataloging\ServiceInterface\CatalogSyndicationMappingServiceInterface;
use App\Cataloging\ValueObject\CategoryDestinationMediaEvaluationRequest;
use App\Cataloging\ValueObject\CategorySyndicationPackageBuildRequest;

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
            CategoryPayloadValueNormalizer::stringList($packagePayload['missingRequiredFields'] ?? null),
            CategoryPayloadValueNormalizer::stringList($strictMedia['requiredMissing'] ?? null),
            CategoryPayloadValueNormalizer::stringList($fallbackMedia['requiredMissing'] ?? null),
            array_merge(
                CategoryPayloadValueNormalizer::stringList($strictMedia['warnings'] ?? null),
                CategoryPayloadValueNormalizer::stringList($fallbackMedia['warnings'] ?? null),
            ),
            CategoryPayloadValueNormalizer::boolMap($strictMedia['checks'] ?? null),
            CategoryPayloadValueNormalizer::boolMap($fallbackMedia['checks'] ?? null),
            CategoryPayloadValueNormalizer::stringList($fallbackMedia['exactMatchedBindingIds'] ?? null),
            CategoryPayloadValueNormalizer::stringList($fallbackMedia['fallbackMatchedBindingIds'] ?? null),
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
