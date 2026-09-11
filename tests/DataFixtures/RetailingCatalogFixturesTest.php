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
                    'types' => [
                        ['code' => 'smart-lock-installation', 'label' => 'Smart Lock Installation'],
                        ['code' => 'video-doorbell-installation', 'label' => 'Video Doorbell Installation'],
                    ],
                ],
            ],
        ];

        $method = new \ReflectionMethod(RetailingCatalogFixtures::class, 'mergeMetadata');
        /** @var array<string, mixed> $merged */
        $merged = $method->invoke(new RetailingCatalogFixtures(), $existing, $fixture);

        self::assertSame('retailing-category@1', $merged['schema']);
        self::assertIsArray($merged['types']);
        /** @var list<array<string, mixed>> $types */
        $types = array_values($merged['types']);
        self::assertCount(2, $types);
        self::assertSame('custom-service', $types[1]['code']);

        $security = $types[0];
        self::assertSame('Security Device Installation', $security['label']);
        self::assertTrue($security['custom']);
        self::assertIsArray($security['types']);
        /** @var list<array<string, mixed>> $securityTypes */
        $securityTypes = array_values($security['types']);
        self::assertCount(3, $securityTypes);
        self::assertSame('custom-sensor-installation', $securityTypes[0]['code']);
        self::assertSame('smart-lock-installation', $securityTypes[1]['code']);
        self::assertSame('Smart Lock Installation', $securityTypes[1]['label']);
        self::assertTrue($securityTypes[1]['custom']);
        self::assertSame('video-doorbell-installation', $securityTypes[2]['code']);
    }

    public function testCanonicalTaxonomyResourcesAreStandaloneAndBridgeFree(): void
    {
        $method = new \ReflectionMethod(RetailingCatalogFixtures::class, 'taxonomyMetadata');
        $fixture = new RetailingCatalogFixtures();

        foreach (['product', 'service', 'project', 'task', 'order'] as $code) {
            /** @var array<string, mixed> $metadata */
            $metadata = $method->invoke($fixture, $code);
            self::assertSame('retailing-category@1', $metadata['schema']);
            self::assertIsArray($metadata['types']);
            self::assertArrayNotHasKey('sourceCatalog', $metadata);
            self::assertStringNotContainsString('sourceCategoryId', json_encode($metadata, JSON_THROW_ON_ERROR));
        }
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

        self::assertIsArray($merged['support']);
        $support = $merged['support'];
        self::assertIsArray($support['dispute'] ?? null);
        $dispute = $support['dispute'];
        self::assertIsArray($dispute['types'] ?? null);
        /** @var list<array<string, mixed>> $types */
        $types = array_values($dispute['types']);
        self::assertSame(['custom', 'quality'], array_column($types, 'code'));
    }
}
