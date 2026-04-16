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
        private ArrayValueNormalizer $arrayValueNormalizer = new ArrayValueNormalizer(),
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
        $packagePayload = $this->mappingService->buildPublishPackage($request)->payload();
        $destinationRequest = new CategoryDestinationMediaEvaluationRequest(
            $context->destinationId(),
            $context->categoryId(),
            $audit,
        );

        $mediaPayload = $this->destinationMediaReadinessService->evaluate($destinationRequest)->payload();
        $report = $this->policy->buildReport(
            $this->arrayValueNormalizer->stringList($packagePayload['missingRequiredFields'] ?? null),
            $this->arrayValueNormalizer->stringList($mediaPayload['requiredMissing'] ?? null),
            $this->arrayValueNormalizer->stringList($mediaPayload['warnings'] ?? null),
            $this->boolMap($mediaPayload['checks'] ?? null),
            $this->arrayValueNormalizer->stringList($mediaPayload['matchedBindingIds'] ?? null),
        );

        return new CategorySyndicationPackageGated(
            [
                'packageId' => trim($context->packageId()),
                'destinationId' => trim($context->destinationId()),
                'categoryId' => trim($context->categoryId()),
                'version' => trim($context->version()),
                'localeMode' => trim($context->localeMode()),
                'payload' => $this->map($packagePayload['payload'] ?? null),
                'fieldMap' => $this->map($packagePayload['fieldMap'] ?? null),
                'requiredFields' => $this->arrayValueNormalizer->stringList($packagePayload['requiredFields'] ?? null),
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

    /** @return array<string,bool> */
    private function boolMap(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        $normalized = [];
        foreach ($value as $key => $item) {
            if (!is_string($key)) {
                continue;
            }
            $normalized[$key] = (bool) $item;
        }

        return $normalized;
    }

    /** @return array<string,mixed> */
    private function map(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        $normalized = [];
        foreach ($value as $key => $item) {
            if (!is_string($key)) {
                continue;
            }
            $normalized[$key] = $item;
        }

        return $normalized;
    }
}
