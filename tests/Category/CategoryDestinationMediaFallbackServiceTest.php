<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Tests\Category;

use App\Policy\CategoryDestinationMediaFallbackPolicy;
use App\Policy\CategorySyndicationDestinationPolicy;
use App\Repository\CategoryMediaBindingRepository;
use App\Repository\CategorySyndicationDestinationRepository;
use App\Service\CategoryDestinationMediaFallbackService;
use App\Service\CategorySyndicationDestinationService;
use PHPUnit\Framework\TestCase;

final class CategoryDestinationMediaFallbackServiceTest extends TestCase
{
    public function testEvaluateBuildsFallbackAwareDestinationMediaReport(): void
    {
        $bindingRepository = new CategoryMediaBindingRepository();
        $destinationRepository = new CategorySyndicationDestinationRepository();

        $destinationService = new CategorySyndicationDestinationService(new CategorySyndicationDestinationPolicy(), $destinationRepository);
        $service = new CategoryDestinationMediaFallbackService($destinationRepository, $bindingRepository, new CategoryDestinationMediaFallbackPolicy());

        $bindingRepository->save(new \App\Entity\CategoryMediaBinding(
            'bind-global-primary',
            'category-1802',
            'asset-primary',
            \App\ValueObject\CategoryMediaRole::primary(),
            [],
            [],
            true,
            true,
            [],
            'operator-1',
            new \DateTimeImmutable('now'),
        ));

        $destinationService->register(
            'destination-1802',
            'Storefront CA French',
            'storefront',
            'push',
            true,
            ['channel' => 'storefront', 'locale' => 'fr_CA', 'requiredMediaRoles' => ['primary']],
            'operator-1',
            'register destination'
        );

        $event = $service->evaluate('destination-1802', 'category-1802', 'operator-9', 'evaluate fallback');
        $payload = $event->payload();

        self::assertFalse($payload['publishable']);
        self::assertTrue($payload['publishableWithFallback']);
        self::assertSame(['bind-global-primary'], $payload['fallbackMatchedBindingIds']);
        self::assertSame([], $payload['requiredMissing']);
        self::assertContains('sharedFallbackUsed', $payload['warnings']);
    }
}
