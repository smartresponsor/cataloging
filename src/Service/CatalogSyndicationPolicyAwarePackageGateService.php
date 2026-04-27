<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Service;

use App\Cataloging\Event\Catalog\CatalogCategorySyndicationPolicyAwarePackageGatedEvent;
use App\Cataloging\EventInterface\Catalog\CatalogCategorySyndicationPolicyAwarePackageGatedEventInterface;
use App\Cataloging\PolicyInterface\CategorySyndicationPolicyAwarePackageGatePolicyInterface;
use App\Cataloging\ServiceInterface\CatalogDestinationMediaPolicyPreferenceServiceInterface;
use App\Cataloging\ServiceInterface\CatalogSyndicationFallbackAwarePackageGateServiceInterface;
use App\Cataloging\ServiceInterface\CatalogSyndicationPolicyAwarePackageGateServiceInterface;
use App\Cataloging\ValueObject\CategoryDestinationMediaEvaluationRequest;
use App\Cataloging\ValueObject\CategorySyndicationPackageBuildRequest;

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
        private ArrayValueNormalizer $arrayValueNormalizer = new ArrayValueNormalizer(),
    ) {
    }

    public function buildGatedPublishPackage(
        CategorySyndicationPackageBuildRequest $request,
    ): CatalogCategorySyndicationPolicyAwarePackageGatedEventInterface {
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
            $this->arrayValueNormalizer->stringList($fallbackAware['packageMissingRequiredFields'] ?? null),
            $preference,
            $fallbackAware,
        );

        return new CatalogCategorySyndicationPolicyAwarePackageGatedEvent([
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
}
