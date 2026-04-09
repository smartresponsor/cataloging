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
/**
 * Provides the catalog syndication fallback aware package gate service application service.
 */
final class CatalogSyndicationFallbackAwarePackageGateService
    implements CatalogSyndicationFallbackAwarePackageGateServiceInterface
{
    /**
     * Initializes the catalog syndication fallback aware package gate service service collaborators.
     */
    public function __construct(
        private readonly CatalogSyndicationMappingServiceInterface $mappingService,
        private readonly CatalogDestinationMediaReadinessServiceInterface $destinationMediaReadinessService,
        private readonly CatalogDestinationMediaFallbackServiceInterface $destinationMediaFallbackService,
        private readonly CategorySyndicationFallbackAwarePackageGatePolicyInterface $policy,
    ) {
    }

    /**
     * @param array<string,mixed>  $categoryData
     * @param array<string,string> $fieldMap
     * @param list<string>         $requiredFields
     */
    public function buildGatedPublishPackage(
        string $packageId,
        string $destinationId,
        string $categoryId,
        string $version,
        string $localeMode,
        array $categoryData,
        array $fieldMap,
        array $requiredFields,
        string $actorId,
        string $reason,
    ): CategorySyndicationFallbackAwarePackageGatedInterface
    {
        $packageBuilt = $this->mappingService->buildPublishPackage(
            $packageId,
            $destinationId,
            $categoryId,
            $version,
            $localeMode,
            $categoryData,
            $fieldMap,
            $requiredFields,
            $actorId,
            $reason,
        );
        $packagePayload = $packageBuilt->payload();

        $strictMedia = $this->destinationMediaReadinessService->evaluate(
            $destinationId,
            $categoryId,
            $actorId,
            $reason,
        )->payload();
        $fallbackMedia = $this->destinationMediaFallbackService->evaluate(
            $destinationId,
            $categoryId,
            $actorId,
            $reason,
        )->payload();

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
                'packageId' => trim($packageId),
                'destinationId' => trim($destinationId),
                'categoryId' => trim($categoryId),
                'version' => trim($version),
                'localeMode' => trim($localeMode),
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
                'actorId' => trim($actorId),
                'reason' => trim($reason),
            ],
            new \DateTimeImmutable(),
        );
    }
}
