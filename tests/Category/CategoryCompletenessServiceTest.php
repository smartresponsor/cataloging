<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Tests\Category;

use App\Policy\CategoryCompletenessPolicy;
use App\Service\CategoryCompletenessService;
use PHPUnit\Framework\TestCase;

final class CategoryCompletenessServiceTest extends TestCase
{
    public function testEvaluateReturnsCompleteReportWithPublicationChecks(): void
    {
        $service = new CategoryCompletenessService(new CategoryCompletenessPolicy());

        $event = $service->evaluate('category-601', [
            'slug' => 'outdoor-lights',
            'seo' => [
                'title' => 'Outdoor Lights',
                'description' => 'Shop outdoor lights',
            ],
            'content' => [
                'body' => 'Long-form merchandising copy',
            ],
            'locale' => [
                'enabled' => ['en_US'],
            ],
            'media' => [
                'primaryAssetId' => 'asset-1',
            ],
            'aliases' => ['garden-lights'],
            'presentation' => [
                'bannerId' => 'banner-1',
                'htmlBlockId' => 'html-1',
            ],
        ], 'operator-1', 'completeness review');

        $payload = $event->payload();
        self::assertTrue($payload['complete']);
        self::assertSame(100, $payload['score']);
        self::assertSame([], $payload['missingRequired']);
        self::assertTrue($payload['publicationChecks']['seoReady']);
        self::assertTrue($payload['publicationChecks']['localeReady']);
    }

    public function testEvaluateReturnsMissingRequiredAndWarningsForIncompletePayload(): void
    {
        $service = new CategoryCompletenessService(new CategoryCompletenessPolicy());

        $event = $service->evaluate('category-602', [
            'slug' => '',
            'seo' => [
                'title' => '',
                'description' => '',
            ],
            'content' => [
                'body' => '',
            ],
            'locale' => [
                'enabled' => [],
            ],
            'media' => [
                'primaryAssetId' => '',
            ],
            'aliases' => [],
            'presentation' => [
                'bannerId' => '',
                'htmlBlockId' => '',
            ],
        ], 'operator-2', 'pre-publish audit');

        $payload = $event->payload();
        self::assertFalse($payload['complete']);
        self::assertContains('slugReady', $payload['missingRequired']);
        self::assertContains('seoTitleReady', $payload['missingRequired']);
        self::assertContains('mediaReady', $payload['warnings']);
        self::assertFalse($payload['publicationChecks']['seoReady']);
    }
}
