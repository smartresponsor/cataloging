<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Service;

use App\Event\CategorySyndicationPackageGated;
use App\EventInterface\CategorySyndicationPackageGatedInterface;
use App\PolicyInterface\CategorySyndicationPackageGatePolicyInterface;
use App\ServiceInterface\CatalogDestinationMediaReadinessServiceInterface;
use App\ServiceInterface\CatalogSyndicationMappingServiceInterface;
use App\ServiceInterface\CatalogSyndicationPackageGateServiceInterface;

final class CatalogSyndicationPackageGateService implements CatalogSyndicationPackageGateServiceInterface
{
    public function __construct(
        private readonly CatalogSyndicationMappingServiceInterface $mappingService,
        private readonly CatalogDestinationMediaReadinessServiceInterface $destinationMediaReadinessService,
        private readonly CategorySyndicationPackageGatePolicyInterface $policy,
    ) {
    }

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
    ): CategorySyndicationPackageGatedInterface {
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

        $mediaReadiness = $this->destinationMediaReadinessService->evaluate($destinationId, $categoryId, $actorId, $reason);
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
                'packageId' => trim($packageId),
                'destinationId' => trim($destinationId),
                'categoryId' => trim($categoryId),
                'version' => trim($version),
                'localeMode' => trim($localeMode),
                'payload' => is_array($packagePayload['payload'] ?? null) ? $packagePayload['payload'] : [],
                'fieldMap' => is_array($packagePayload['fieldMap'] ?? null) ? $packagePayload['fieldMap'] : [],
                'requiredFields' => is_array($packagePayload['requiredFields'] ?? null) ? $packagePayload['requiredFields'] : [],
                'packageMissingRequiredFields' => $report->packageMissingRequiredFields(),
                'mediaRequiredMissing' => $report->mediaRequiredMissing(),
                'warnings' => $report->warnings(),
                'checks' => $report->checks(),
                'matchedBindingIds' => $report->matchedBindingIds(),
                'publishable' => $report->publishable(),
                'actorId' => trim($actorId),
                'reason' => trim($reason),
            ],
            new \DateTimeImmutable(),
        );
    }
}
