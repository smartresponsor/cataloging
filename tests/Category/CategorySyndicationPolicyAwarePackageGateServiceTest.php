<?php

declare(strict_types=1);

namespace App\Tests\Category;

use App\Policy\CategorySyndicationPolicyAwarePackageGatePolicy;
use App\Service\CategorySyndicationPolicyAwarePackageGateService;
use App\ServiceInterface\CategoryDestinationMediaPolicyPreferenceServiceInterface;
use App\ServiceInterface\CategorySyndicationFallbackAwarePackageGateServiceInterface;
use PHPUnit\Framework\TestCase;

final class CategorySyndicationPolicyAwarePackageGateServiceTest extends TestCase
{
    public function testBuildGatedPublishPackageResolvesPublishabilityViaPolicy(): void
    {
        $fallbackAwareGateService = new class implements CategorySyndicationFallbackAwarePackageGateServiceInterface {
            public function buildGatedPublishPackage(string $packageId, string $destinationId, string $categoryId, string $version, string $localeMode, array $categoryData, array $fieldMap, array $requiredFields, string $actorId, string $reason): \App\EventInterface\CategorySyndicationFallbackAwarePackageGatedInterface
            {
                return new \App\Event\CategorySyndicationFallbackAwarePackageGated([
                    'packageId' => $packageId,
                    'destinationId' => $destinationId,
                    'categoryId' => $categoryId,
                    'version' => $version,
                    'localeMode' => $localeMode,
                    'payload' => ['slug' => 'chairs'],
                    'fieldMap' => $fieldMap,
                    'requiredFields' => $requiredFields,
                    'packageMissingRequiredFields' => [],
                    'warnings' => ['package_publishable_via_fallback_only'],
                    'checks' => ['fallbackPackageGatePublishable' => true],
                    'exactMatchedBindingIds' => ['m1'],
                    'fallbackMatchedBindingIds' => ['m2'],
                ], new \DateTimeImmutable());
            }
        };

        $preferenceService = new class implements CategoryDestinationMediaPolicyPreferenceServiceInterface {
            public function evaluate(string $destinationId, string $categoryId, string $actorId, string $reason): \App\EventInterface\CategoryDestinationMediaPolicyPreferenceEvaluatedInterface
            {
                return new \App\Event\CategoryDestinationMediaPolicyPreferenceEvaluated([
                    'destinationId' => $destinationId,
                    'categoryId' => $categoryId,
                    'mediaPolicyMode' => 'allow_fallback',
                    'strictPublishable' => false,
                    'fallbackPublishable' => true,
                    'resolvedPublishable' => true,
                    'fallbackUsed' => true,
                    'requiredMissing' => [],
                    'warnings' => ['destination_media_policy_preferred_exact_fallback_used'],
                    'checks' => ['resolvedPublishable' => true],
                    'actorId' => $actorId,
                    'reason' => $reason,
                ], new \DateTimeImmutable());
            }
        };

        $service = new CategorySyndicationPolicyAwarePackageGateService(
            $fallbackAwareGateService,
            $preferenceService,
            new CategorySyndicationPolicyAwarePackageGatePolicy(),
        );

        $event = $service->buildGatedPublishPackage('pkg-1', 'dst-1', 'cat-1', 'v1', 'per_locale', ['slug' => 'chairs'], ['slug' => 'slug'], ['slug'], 'actor-1', 'test');
        $payload = $event->payload();

        self::assertSame('allow_fallback', $payload['mediaPolicyMode']);
        self::assertTrue($payload['resolvedPublishable']);
        self::assertTrue($payload['fallbackUsed']);
        self::assertContains('package_publishable_by_destination_media_policy_fallback', $payload['warnings']);
    }
}
