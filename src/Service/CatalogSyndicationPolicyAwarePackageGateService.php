<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\Event\CategorySyndicationPolicyAwarePackageGated;
use App\EventInterface\CategorySyndicationPolicyAwarePackageGatedInterface;
use App\PolicyInterface\CategorySyndicationPolicyAwarePackageGatePolicyInterface;
use App\ServiceInterface\CatalogDestinationMediaPolicyPreferenceServiceInterface;
use App\ServiceInterface\CatalogSyndicationFallbackAwarePackageGateServiceInterface;
use App\ServiceInterface\CatalogSyndicationPolicyAwarePackageGateServiceInterface;
/**
 * Provides the catalog syndication policy aware package gate service application service.
 */
final class CatalogSyndicationPolicyAwarePackageGateService
    implements CatalogSyndicationPolicyAwarePackageGateServiceInterface
{
    /**
     * Initializes the catalog syndication policy aware package gate service service collaborators.
     */
    public function __construct(
        private readonly CatalogSyndicationFallbackAwarePackageGateServiceInterface $fallbackAwareGateService,
        private readonly CatalogDestinationMediaPolicyPreferenceServiceInterface $preferenceService,
        private readonly CategorySyndicationPolicyAwarePackageGatePolicyInterface $policy,
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
    ): CategorySyndicationPolicyAwarePackageGatedInterface
    {
        $fallbackAware = $this->fallbackAwareGateService->buildGatedPublishPackage(
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
        )->payload();
        $preference = $this->preferenceService->evaluate($destinationId, $categoryId, $actorId, $reason)->payload();
        $report = $this->policy->buildReport(
            is_array($fallbackAware['packageMissingRequiredFields'] ?? null)
                ? $fallbackAware['packageMissingRequiredFields']
                : [],
            $preference,
            $fallbackAware,
        );

        return new CategorySyndicationPolicyAwarePackageGated([
            'packageId' => trim($packageId),
            'destinationId' => trim($destinationId),
            'categoryId' => trim($categoryId),
            'version' => trim($version),
            'localeMode' => trim($localeMode),
            'payload' => is_array($fallbackAware['payload'] ?? null) ? $fallbackAware['payload'] : [],
            'fieldMap' => is_array($fallbackAware['fieldMap'] ?? null) ? $fallbackAware['fieldMap'] : [],
            'requiredFields' => is_array($fallbackAware['requiredFields'] ?? null)
                ? $fallbackAware['requiredFields']
                : [],
            'mediaPolicyMode' => $report->mediaPolicyMode(),
            'resolvedPublishable' => $report->resolvedPublishable(),
            'fallbackUsed' => $report->fallbackUsed(),
            'warnings' => $report->warnings(),
            'checks' => $report->checks(),
            'exactMatchedBindingIds' => $report->exactMatchedBindingIds(),
            'fallbackMatchedBindingIds' => $report->fallbackMatchedBindingIds(),
            'actorId' => trim($actorId),
            'reason' => trim($reason),
        ], new \DateTimeImmutable());
    }
}
