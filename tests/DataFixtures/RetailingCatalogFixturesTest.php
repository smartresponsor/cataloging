<?php

declare(strict_types=1);

namespace App\Cataloging\Tests\DataFixtures;

use App\Cataloging\DataFixtures\RetailingCatalogFixtures;
use PHPUnit\Framework\TestCase;

final class RetailingCatalogFixturesTest extends TestCase
{
    public function testMergeMetadataPreservesExistingTopLevelAndNestedTypes(): void
    {
        $existing = [
            'schema' => 'retailing-category@1',
            'types' => [
                [
                    'code' => 'security-device-installation',
                    'label' => 'Security Devices',
                    'custom' => true,
                    'types' => [
                        ['code' => 'custom-sensor-installation', 'label' => 'Custom Sensor Installation'],
                        ['code' => 'smart-lock-installation', 'label' => 'Existing Smart Lock Label', 'custom' => true],
                    ],
                ],
                ['code' => 'custom-service', 'label' => 'Custom Service'],
            ],
        ];
        $fixture = [
            'schema' => 'retailing-category@1',
            'types' => [
                [
                    'code' => 'security-device-installation',
                    'label' => 'Security Device Installation',
                    'sourceCategoryId' => '861',
                    'types' => [
                        ['code' => 'smart-lock-installation', 'label' => 'Smart Lock Installation', 'sourceCategoryId' => '864'],
                        ['code' => 'video-doorbell-installation', 'label' => 'Video Doorbell Installation', 'sourceCategoryId' => '863'],
                    ],
                ],
            ],
        ];

        $method = new \ReflectionMethod(RetailingCatalogFixtures::class, 'mergeMetadata');
        /** @var array<string, mixed> $merged */
        $merged = $method->invoke(new RetailingCatalogFixtures(), $existing, $fixture);

        self::assertSame('retailing-category@1', $merged['schema']);
        self::assertCount(2, $merged['types']);
        self::assertSame('custom-service', $merged['types'][1]['code']);

        $security = $merged['types'][0];
        self::assertSame('Security Device Installation', $security['label']);
        self::assertSame('861', $security['sourceCategoryId']);
        self::assertTrue($security['custom']);
        self::assertCount(3, $security['types']);
        self::assertSame('custom-sensor-installation', $security['types'][0]['code']);
        self::assertSame('smart-lock-installation', $security['types'][1]['code']);
        self::assertSame('Smart Lock Installation', $security['types'][1]['label']);
        self::assertSame('864', $security['types'][1]['sourceCategoryId']);
        self::assertTrue($security['types'][1]['custom']);
        self::assertSame('video-doorbell-installation', $security['types'][2]['code']);
    }

    public function testMergeMetadataPreservesExistingSupportVocabularyTypes(): void
    {
        $method = new \ReflectionMethod(RetailingCatalogFixtures::class, 'mergeMetadata');
        /** @var array<string, mixed> $merged */
        $merged = $method->invoke(new RetailingCatalogFixtures(), [
            'support' => ['dispute' => ['types' => [['code' => 'custom', 'label' => 'Custom']]]],
        ], [
            'support' => ['dispute' => ['types' => [['code' => 'quality', 'label' => 'Quality']]]],
        ]);

        self::assertSame(['custom', 'quality'], array_column($merged['support']['dispute']['types'], 'code'));
    }
}
