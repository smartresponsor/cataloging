<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Cataloging\Tests\Category;

use App\Cataloging\Policy\CategoryPublicationQualityPolicy;
use App\Cataloging\Service\CatalogPublicationQualityService;
use App\Cataloging\ValueObject\CatalogAuditContext;
use App\Cataloging\ValueObject\CategoryPublicationQualityEvaluationRequest;
use App\Cataloging\ValueObject\CategoryPublicationQualityInput;
use PHPUnit\Framework\TestCase;

final class CatalogPublicationQualityServiceTest extends TestCase
{
    public function testEvaluateReturnsAttentionPayloadForOptionalGaps(): void
    {
        $service = new CatalogPublicationQualityService(new CategoryPublicationQualityPolicy());

        $payload = $this->normalizePayload($service->evaluate(
            new CategoryPublicationQualityEvaluationRequest(
                new CategoryPublicationQualityInput(
                    'category-701',
                    78,
                    [
                        'slugReady' => true,
                        'seoReady' => true,
                        'contentReady' => true,
                        'localeReady' => true,
                        'mediaReady' => false,
                        'slugHistoryReady' => false,
                    ],
                    [
                        'bannerReady' => false,
                        'htmlBlockReady' => true,
                    ],
                ),
                new CatalogAuditContext('operator-1', 'pre-publish quality evaluation'),
            ),
        )->payload());

        self::assertTrue($payload['publishableQuality']);
        self::assertSame('attention', $payload['riskLevel']);
        self::assertContains('mediaReady', $payload['softWarnings']);
        self::assertContains('slugHistoryReady', $payload['softWarnings']);
        self::assertContains('qualityScoreBelowTarget', $payload['softWarnings']);
        self::assertContains('bannerReady', $payload['advisoryWarnings']);
    }

    /**
     * @param array<string, mixed> $payload
     *
     * @return array{publishableQuality: bool, riskLevel: string, softWarnings: list<string>, advisoryWarnings: list<string>}
     */
    private function normalizePayload(array $payload): array
    {
        return [
            'publishableQuality' => (bool) ($payload['publishableQuality'] ?? false),
            'riskLevel' => $this->scalarString($payload['riskLevel'] ?? ''),
            'softWarnings' => $this->stringList($payload['softWarnings'] ?? []),
            'advisoryWarnings' => $this->stringList($payload['advisoryWarnings'] ?? []),
        ];
    }

    /** @return list<string> */
    private function stringList(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        return array_values(array_map(fn (mixed $item): string => $this->scalarString($item), $value));
    }

    private function scalarString(mixed $value): string
    {
        return is_scalar($value) ? (string) $value : '';
    }
}
