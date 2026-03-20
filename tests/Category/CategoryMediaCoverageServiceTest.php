<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Tests\Category;

use App\Policy\CategoryMediaCoveragePolicy;
use App\Policy\CategoryMediaGovernancePolicy;
use App\Repository\CategoryMediaBindingRepository;
use App\Service\CategoryMediaCoverageService;
use App\Service\CategoryMediaGovernanceService;
use PHPUnit\Framework\TestCase;

final class CategoryMediaCoverageServiceTest extends TestCase
{
    public function testEvaluateReturnsGovernedMediaChecks(): void
    {
        $repository = new CategoryMediaBindingRepository();
        $governance = new CategoryMediaGovernanceService($repository, new CategoryMediaGovernancePolicy());
        $coverage = new CategoryMediaCoverageService($repository, new CategoryMediaCoveragePolicy());

        $governance->bind('bind-1', 'category-901', 'asset-primary', 'primary', ['storefront'], ['en_US'], true, true, [], 'operator-1', 'bind primary');
        $governance->bind('bind-2', 'category-901', 'asset-banner', 'banner', ['storefront'], ['en_US'], false, true, [], 'operator-1', 'bind banner');

        $event = $coverage->evaluate('category-901', [
            'media' => ['primaryAssetId' => ''],
            'presentation' => ['bannerId' => ''],
        ], 'operator-2', 'media readiness review');

        $payload = $event->payload();
        self::assertSame([], $payload['requiredMissing']);
        self::assertTrue($payload['checks']['mediaReady']);
        self::assertTrue($payload['checks']['bannerReady']);
        self::assertTrue($payload['checks']['requiredMediaCoverageReady']);
        self::assertContains('heroReady', $payload['warnings']);
    }
}
