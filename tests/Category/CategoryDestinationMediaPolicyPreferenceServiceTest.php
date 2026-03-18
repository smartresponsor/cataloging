<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Tests\Category;

use App\Entity\CategoryMediaBinding;
use App\Entity\CategorySyndicationDestination;
use App\Policy\CategoryDestinationMediaFallbackPolicy;
use App\Policy\CategoryDestinationMediaPolicyPreferencePolicy;
use App\Policy\CategoryDestinationMediaReadinessPolicy;
use App\Repository\CategoryMediaBindingRepository;
use App\Repository\CategorySyndicationDestinationRepository;
use App\Service\CategoryDestinationMediaFallbackService;
use App\Service\CategoryDestinationMediaPolicyPreferenceService;
use App\Service\CategoryDestinationMediaReadinessService;
use App\Service\CategoryMediaApplicabilityService;
use App\ValueObject\CategoryMediaRole;
use PHPUnit\Framework\TestCase;

final class CategoryDestinationMediaPolicyPreferenceServiceTest extends TestCase
{
    public function testEvaluateResolvesPublishableViaFallbackWhenDestinationAllowsIt(): void
    {
        $destinationRepository = new CategorySyndicationDestinationRepository();
        $destinationRepository->save(CategorySyndicationDestination::register(
            'destination-1901',
            'Storefront Feed',
            'storefront',
            'push',
            true,
            [
                'channel' => 'storefront',
                'locale' => 'en_US',
                'requiredMediaRoles' => ['primary'],
                'mediaPolicyMode' => 'allow_fallback',
            ],
            'operator-1',
        ));

        $bindingRepository = new CategoryMediaBindingRepository();
        $bindingRepository->save(new CategoryMediaBinding(
            'binding-global-primary',
            'category-1901',
            'asset-global-primary',
            CategoryMediaRole::primary(),
            [],
            [],
            true,
            true,
            [],
            'operator-1',
            new \DateTimeImmutable('now'),
        ));

        $service = new CategoryDestinationMediaPolicyPreferenceService(
            $destinationRepository,
            new CategoryDestinationMediaReadinessService($destinationRepository, new CategoryMediaApplicabilityService($bindingRepository, new \App\Policy\CategoryMediaApplicabilityPolicy()), new CategoryDestinationMediaReadinessPolicy()),
            new CategoryDestinationMediaFallbackService($destinationRepository, $bindingRepository, new CategoryDestinationMediaFallbackPolicy()),
            new CategoryDestinationMediaPolicyPreferencePolicy(),
        );

        $payload = $service->evaluate('destination-1901', 'category-1901', 'operator-1', 'step08')->payload();

        self::assertSame('allow_fallback', $payload['mediaPolicyMode']);
        self::assertFalse($payload['strictPublishable']);
        self::assertTrue($payload['fallbackPublishable']);
        self::assertTrue($payload['resolvedPublishable']);
        self::assertTrue($payload['checks']['fallbackAcceptedByPolicy']);
    }
}
