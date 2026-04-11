<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\Event\CategorySyndicationPackageGated;
use App\EventInterface\CategorySyndicationPackageGatedInterface;
use App\PolicyInterface\CategorySyndicationPackageGatePolicyInterface;
use App\ServiceInterface\CatalogDestinationMediaReadinessServiceInterface;
use App\ServiceInterface\CatalogSyndicationMappingServiceInterface;
use App\ServiceInterface\CatalogSyndicationPackageGateServiceInterface;
use App\ValueObject\CategoryDestinationMediaEvaluationRequest;
use App\ValueObject\CategorySyndicationPackageBuildRequest;

/**
 * Provides the catalog syndication package gate service application service.
 */
final readonly class CatalogSyndicationPackageGateService implements CatalogSyndicationPackageGateServiceInterface
{
    /**
     * Initializes the catalog syndication package gate service service collaborators.
     */
    public function __construct(
        private CatalogSyndicationMappingServiceInterface $mappingService,
        private CatalogDestinationMediaReadinessServiceInterface $destinationMediaReadinessService,
        private CategorySyndicationPackageGatePolicyInterface $policy,
    ) {
    }

    /**
     * Builds the gated publish package result for the current workflow.
     */
    public function buildGatedPublishPackage(
        CategorySyndicationPackageBuildRequest $request,
    ): CategorySyndicationPackageGatedInterface {
        $context = $request->context();
        $audit = $request->auditContext();
        $packageBuilt = $this->mappingService->buildPublishPackage($request);
        $packagePayload = $packageBuilt->payload();
        $destinationRequest = new CategoryDestinationMediaEvaluationRequest(
            $context->destinationId(),
            $context->categoryId(),
            $audit,
        );

        $mediaReadiness = $this->destinationMediaReadinessService->evaluate($destinationRequest);
        $mediaPayload = $mediaReadiness->payload();

        $report = $this->policy->buildReport(
            is_array($packagePayload['missingRequiredFields'] ?? null) ? $packagePayload['missingRequiredFields'] : [],
            is_array($mediaPayload['requiredMissing'] ?? null) ? $mediaPayload['requiredMissing'] : [],
            is_array($mediaPayload['warnings'] ?? null) ? $mediaPayload['warnings'] : [],
            is_array($mediaPayload['checks'] ?? null) ? $mediaPayload['checks'] : [],
            is_array($mediaPayload['matchedBindingIds'] ?? null) ? $mediaPayload['matchedBindingIds'] : [],
        );

        return new CategorySyndicationPackageGated(
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
                'mediaRequiredMissing' => $report->mediaRequiredMissing(),
                'warnings' => $report->warnings(),
                'checks' => $report->checks(),
                'matchedBindingIds' => $report->matchedBindingIds(),
                'publishable' => $report->publishable(),
                'actorId' => trim($audit->actorId()),
                'reason' => trim($audit->reason()),
            ],
            new \DateTimeImmutable(),
        );
    }
}
