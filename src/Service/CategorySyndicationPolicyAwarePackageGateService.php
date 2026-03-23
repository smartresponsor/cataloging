<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\Event\CategorySyndicationPolicyAwarePackageGated;
use App\EventInterface\CategorySyndicationPolicyAwarePackageGatedInterface;
use App\PolicyInterface\CategorySyndicationPolicyAwarePackageGatePolicyInterface;
use App\ServiceInterface\CategoryDestinationMediaPolicyPreferenceServiceInterface;
use App\ServiceInterface\CategorySyndicationFallbackAwarePackageGateServiceInterface;
use App\ServiceInterface\CategorySyndicationPolicyAwarePackageGateServiceInterface;

final class CategorySyndicationPolicyAwarePackageGateService implements CategorySyndicationPolicyAwarePackageGateServiceInterface
{
    public function __construct(
        private readonly CategorySyndicationFallbackAwarePackageGateServiceInterface $fallbackAwareGateService,
        private readonly CategoryDestinationMediaPolicyPreferenceServiceInterface $destinationMediaPolicyPreferenceService,
        private readonly CategorySyndicationPolicyAwarePackageGatePolicyInterface $policy,
    ) {
    }

    public function buildGatedPublishPackage(string $packageId, string $destinationId, string $categoryId, string $version, string $localeMode, array $categoryData, array $fieldMap, array $requiredFields, string $actorId, string $reason): CategorySyndicationPolicyAwarePackageGatedInterface
    {
        $fallbackGatePayload = $this->fallbackAwareGateService->buildGatedPublishPackage($packageId, $destinationId, $categoryId, $version, $localeMode, $categoryData, $fieldMap, $requiredFields, $actorId, $reason)->payload();
        $policyPayload = $this->destinationMediaPolicyPreferenceService->evaluate($destinationId, $categoryId, $actorId, $reason)->payload();

        $report = $this->policy->buildReport(
            is_array($fallbackGatePayload['packageMissingRequiredFields'] ?? null) ? $fallbackGatePayload['packageMissingRequiredFields'] : [],
            $policyPayload,
            $fallbackGatePayload,
        );

        return new CategorySyndicationPolicyAwarePackageGated(
            [
                'packageId' => trim($packageId),
                'destinationId' => trim($destinationId),
                'categoryId' => trim($categoryId),
                'version' => trim($version),
                'localeMode' => trim($localeMode),
                'payload' => is_array($fallbackGatePayload['payload'] ?? null) ? $fallbackGatePayload['payload'] : [],
                'fieldMap' => is_array($fallbackGatePayload['fieldMap'] ?? null) ? $fallbackGatePayload['fieldMap'] : [],
                'requiredFields' => is_array($fallbackGatePayload['requiredFields'] ?? null) ? $fallbackGatePayload['requiredFields'] : [],
                'mediaPolicyMode' => $report->mediaPolicyMode(),
                'packageMissingRequiredFields' => $report->packageMissingRequiredFields(),
                'requiredMissing' => $report->requiredMissing(),
                'warnings' => $report->warnings(),
                'checks' => $report->checks(),
                'exactMatchedBindingIds' => $report->exactMatchedBindingIds(),
                'fallbackMatchedBindingIds' => $report->fallbackMatchedBindingIds(),
                'strictPublishable' => $report->strictPublishable(),
                'fallbackPublishable' => $report->fallbackPublishable(),
                'resolvedPublishable' => $report->resolvedPublishable(),
                'fallbackUsed' => $report->fallbackUsed(),
                'actorId' => trim($actorId),
                'reason' => trim($reason),
            ],
            new \DateTimeImmutable(),
        );
    }
}
