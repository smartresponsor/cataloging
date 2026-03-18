<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Tests\Category;

use App\Policy\CategoryPublicationQualityPolicy;
use App\Service\CategoryPublicationQualityService;
use PHPUnit\Framework\TestCase;

final class CategoryPublicationQualityServiceTest extends TestCase
{
    public function testEvaluateReturnsAttentionPayloadForOptionalGaps(): void
    {
        $service = new CategoryPublicationQualityService(new CategoryPublicationQualityPolicy());

        $event = $service->evaluate(
            'category-701',
            78,
            [
                'slugReady' => true,
                'seoReady' => true,
                'contentReady' => true,
                'localeReady' => true,
                'mediaReady' => false,
                'aliasReady' => false,
            ],
            [
                'bannerReady' => false,
                'htmlBlockReady' => true,
            ],
            'operator-1',
            'pre-publish quality evaluation',
        );

        $payload = $event->payload();
        self::assertTrue($payload['publishableQuality']);
        self::assertSame('attention', $payload['riskLevel']);
        self::assertContains('mediaReady', $payload['softWarnings']);
        self::assertContains('aliasReady', $payload['softWarnings']);
        self::assertContains('qualityScoreBelowTarget', $payload['softWarnings']);
        self::assertContains('bannerReady', $payload['advisoryWarnings']);
    }
}
