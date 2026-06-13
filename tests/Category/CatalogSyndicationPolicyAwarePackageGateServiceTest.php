<?php

declare(strict_types=1);
/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>
 * Owner: Marketing America Corp
 */

namespace App\Cataloging\Tests\CategoryEntity;

use App\Cataloging\Policy\CategorySyndicationPolicyAwarePackageGatePolicy;
use App\Cataloging\Service\CatalogSyndicationPolicyAwarePackageGateService;
use App\Cataloging\ServiceInterface\CatalogDestinationMediaPolicyPreferenceServiceInterface;
use App\Cataloging\ServiceInterface\CatalogSyndicationFallbackAwarePackageGateServiceInterface;
use App\Cataloging\ValueObject\CatalogAuditContext;
use App\Cataloging\ValueObject\CategoryDestinationMediaEvaluationRequest;
use App\Cataloging\ValueObject\CategorySyndicationPackageBuildRequest;
use App\Cataloging\ValueObject\CategorySyndicationPackageContext;
use PHPUnit\Framework\TestCase;

final class CatalogSyndicationPolicyAwarePackageGateServiceTest extends TestCase
{
    public function testBuildGatedPublishPackageResolvesPublishabilityViaPolicy(): void
    {
        $fallbackAwareGateService = new class implements CatalogSyndicationFallbackAwarePackageGateServiceInterface {
            public function buildGatedPublishPackage(CategorySyndicationPackageBuildRequest $request): \App\Cataloging\EventInterface\Catalog\CatalogCategorySyndicationFallbackAwarePackageGatedEventInterface
            {
                $context = $request->context();

                return new \App\Cataloging\Event\Catalog\CatalogCategorySyndicationFallbackAwarePackageGatedEvent([
                    'packageId' => $context->packageId(),
                    'destinationId' => $context->destinationId(),
                    'categoryId' => $context->categoryId(),
                    'version' => $context->version(),
                    'localeMode' => $context->localeMode(),
                    'payload' => ['slug' => 'chairs'],
                    'fieldMap' => $request->fieldMap(),
                    'requiredFields' => $request->requiredFields(),
                    'packageMissingRequiredFields' => [],
                    'warnings' => ['package_publishable_via_fallback_only'],
                    'checks' => ['fallbackPackageGatePublishable' => true],
                    'exactMatchedBindingIds' => ['m1'],
                    'fallbackMatchedBindingIds' => ['m2'],
                ], new \DateTimeImmutable());
            }
        };

        $preferenceService = new class implements CatalogDestinationMediaPolicyPreferenceServiceInterface {
            public function evaluate(CategoryDestinationMediaEvaluationRequest $request): \App\Cataloging\EventInterface\Catalog\CatalogCategoryDestinationMediaPolicyPreferenceEvaluatedEventInterface
            {
                return new \App\Cataloging\Event\Catalog\CatalogCategoryDestinationMediaPolicyPreferenceEvaluatedEvent([
                    'destinationId' => $request->destinationId(),
                    'categoryId' => $request->categoryId(),
                    'mediaPolicyMode' => 'allow_fallback',
                    'strictPublishable' => false,
                    'fallbackPublishable' => true,
                    'resolvedPublishable' => true,
                    'fallbackUsed' => true,
                    'requiredMissing' => [],
                    'warnings' => ['destination_media_policy_preferred_exact_fallback_used'],
                    'checks' => ['resolvedPublishable' => true],
                    'actorId' => $request->actorId(),
                    'reason' => $request->reason(),
                ], new \DateTimeImmutable());
            }
        };

        $service = new CatalogSyndicationPolicyAwarePackageGateService(
            $fallbackAwareGateService,
            $preferenceService,
            new CategorySyndicationPolicyAwarePackageGatePolicy(),
        );

        $event = $service->buildGatedPublishPackage(
            new CategorySyndicationPackageBuildRequest(
                new CategorySyndicationPackageContext('pkg-1', 'dst-1', 'cat-1', 'v1', 'per_locale'),
                ['slug' => 'chairs'],
                ['slug' => 'slug'],
                ['slug'],
                new CatalogAuditContext('actor-1', 'test'),
            ),
        );
        $payload = $event->payload();
        self::assertIsArray($payload['warnings'] ?? null);
        /** @var list<string> $warnings */
        $warnings = $payload['warnings'];

        self::assertSame('allow_fallback', $payload['mediaPolicyMode']);
        self::assertTrue($payload['resolvedPublishable']);
        self::assertTrue($payload['fallbackUsed']);
        self::assertContains('package_publishable_by_destination_media_policy_fallback', $warnings);
    }
}
