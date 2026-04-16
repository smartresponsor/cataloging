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
use App\ValueObject\CategoryDestinationMediaEvaluationRequest;
use App\ValueObject\CategorySyndicationPackageBuildRequest;

/**
 * Provides the catalog syndication policy aware package gate service application service.
 */
final readonly class CatalogSyndicationPolicyAwarePackageGateService implements CatalogSyndicationPolicyAwarePackageGateServiceInterface
{
    /**
     * Initializes the catalog syndication policy aware package gate service service collaborators.
     */
    public function __construct(
        private CatalogSyndicationFallbackAwarePackageGateServiceInterface $fallbackAwareGateService,
        private CatalogDestinationMediaPolicyPreferenceServiceInterface $preferenceService,
        private CategorySyndicationPolicyAwarePackageGatePolicyInterface $policy,
    ) {
    }

    public function buildGatedPublishPackage(
        CategorySyndicationPackageBuildRequest $request,
    ): CategorySyndicationPolicyAwarePackageGatedInterface {
        $context = $request->context();
        $audit = $request->auditContext();
        $fallbackAware = $this->fallbackAwareGateService->buildGatedPublishPackage($request)->payload();
        $preference = $this->preferenceService->evaluate(
            new CategoryDestinationMediaEvaluationRequest(
                $context->destinationId(),
                $context->categoryId(),
                $audit,
            ),
        )->payload();
        $report = $this->policy->buildReport(
            $this->stringList($fallbackAware['packageMissingRequiredFields'] ?? null),
            $preference,
            $fallbackAware,
        );

        return new CategorySyndicationPolicyAwarePackageGated([
            'packageId' => trim($context->packageId()),
            'destinationId' => trim($context->destinationId()),
            'categoryId' => trim($context->categoryId()),
            'version' => trim($context->version()),
            'localeMode' => trim($context->localeMode()),
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
            'actorId' => trim($audit->actorId()),
            'reason' => trim($audit->reason()),
        ], new \DateTimeImmutable());
    }

    /** @return list<string> */
    private function stringList(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        $normalized = [];
        foreach ($value as $item) {
            if (!is_scalar($item)) {
                continue;
            }
            $trimmed = trim((string) $item);
            if ('' !== $trimmed) {
                $normalized[] = $trimmed;
            }
        }

        return $normalized;
    }
}
