<?php

declare(strict_types=1);
/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>
 * Owner: Marketing America Corp
 */

namespace App\Tests\Category;

use App\Policy\CategorySyndicationPolicyAwarePackageGatePolicy;
use App\Service\CatalogSyndicationPolicyAwarePackageGateService;
use App\ServiceInterface\CatalogDestinationMediaPolicyPreferenceServiceInterface;
use App\ServiceInterface\CatalogSyndicationFallbackAwarePackageGateServiceInterface;
use App\ValueObject\CatalogAuditContext;
use App\ValueObject\CategoryDestinationMediaEvaluationRequest;
use App\ValueObject\CategorySyndicationPackageBuildRequest;
use App\ValueObject\CategorySyndicationPackageContext;
use PHPUnit\Framework\TestCase;

final class CatalogSyndicationPolicyAwarePackageGateServiceTest extends TestCase
{
    public function testBuildGatedPublishPackageResolvesPublishabilityViaPolicy(): void
    {
        $fallbackAwareGateService = new class implements CatalogSyndicationFallbackAwarePackageGateServiceInterface {
            public function buildGatedPublishPackage(CategorySyndicationPackageBuildRequest $request): \App\EventInterface\CategorySyndicationFallbackAwarePackageGatedInterface
            {
                $context = $request->context();

                return new \App\Event\CategorySyndicationFallbackAwarePackageGated([
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
            public function evaluate(CategoryDestinationMediaEvaluationRequest $request): \App\EventInterface\CategoryDestinationMediaPolicyPreferenceEvaluatedInterface
            {
                return new \App\Event\CategoryDestinationMediaPolicyPreferenceEvaluated([
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
