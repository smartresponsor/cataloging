<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\Event\CategorySyndicationPolicyAwarePackageGated;
use App\Cataloging\EventInterface\CategorySyndicationPolicyAwarePackageGatedInterface;
use App\Cataloging\PolicyInterface\CategorySyndicationPolicyAwarePackageGatePolicyInterface;
use App\Cataloging\ServiceInterface\CatalogDestinationMediaPolicyPreferenceServiceInterface;
use App\Cataloging\ServiceInterface\CatalogSyndicationFallbackAwarePackageGateServiceInterface;
use App\Cataloging\ServiceInterface\CatalogSyndicationPolicyAwarePackageGateServiceInterface;

final class CatalogSyndicationPolicyAwarePackageGateService implements CatalogSyndicationPolicyAwarePackageGateServiceInterface
{
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
    public function buildGatedPublishPackage(string $packageId, string $destinationId, string $categoryId, string $version, string $localeMode, array $categoryData, array $fieldMap, array $requiredFields, string $actorId, string $reason): CategorySyndicationPolicyAwarePackageGatedInterface
    {
        $fallbackAware = $this->fallbackAwareGateService->buildGatedPublishPackage($packageId, $destinationId, $categoryId, $version, $localeMode, $categoryData, $fieldMap, $requiredFields, $actorId, $reason)->payload();
        $preference = $this->preferenceService->evaluate($destinationId, $categoryId, $actorId, $reason)->payload();
        $report = $this->policy->buildReport(
            is_array($fallbackAware['packageMissingRequiredFields'] ?? null) ? $fallbackAware['packageMissingRequiredFields'] : [],
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
            'requiredFields' => is_array($fallbackAware['requiredFields'] ?? null) ? $fallbackAware['requiredFields'] : [],
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
