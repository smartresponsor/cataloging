<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Cataloging\Tests\CategoryEntity;

use App\Cataloging\Policy\CategoryCompletenessPolicy;
use App\Cataloging\Policy\CategoryMediaCoveragePolicy;
use App\Cataloging\Policy\CategoryPublicationQualityPolicy;
use App\Cataloging\Repository\Catalog\CatalogCategoryMediaBindingRepository;
use App\Cataloging\Service\CatalogMediaCompletenessBridgeService;
use App\Cataloging\Service\CatalogMediaCoverageService;
use App\Cataloging\Service\CatalogMediaPublicationQualityBridgeService;
use App\Cataloging\Service\CatalogPublicationQualityService;
use App\Cataloging\ValueObject\CatalogAuditContext;
use App\Cataloging\ValueObject\CategoryEvaluationRequest;
use PHPUnit\Framework\TestCase;

final class CatalogMediaPublicationQualityBridgeServiceTest extends TestCase
{
    public function testEvaluatePromotesMissingRequiredMediaCoverageToHardBlocker(): void
    {
        $repository = new CatalogCategoryMediaBindingRepository();
        $coverage = new CatalogMediaCoverageService($repository, new CategoryMediaCoveragePolicy());
        $completenessBridge = new CatalogMediaCompletenessBridgeService(new CategoryCompletenessPolicy(), $coverage);
        $qualityBridge = new CatalogMediaPublicationQualityBridgeService($completenessBridge, new CatalogPublicationQualityService(new CategoryPublicationQualityPolicy()));

        $payload = $this->normalizePayload($qualityBridge->evaluate(new CategoryEvaluationRequest('category-903', [
            'slug' => 'clearance',
            'seo' => ['title' => 'Clearance', 'description' => 'Clearance offers'],
            'content' => ['body' => 'Discounted products'],
            'locale' => ['enabled' => ['en_US']],
            'media' => ['primaryAssetId' => 'asset-inline'],
            'slugHistories' => ['sale'],
            'presentation' => ['bannerId' => '', 'htmlBlockId' => 'html-9'],
        ], new CatalogAuditContext('operator-3', 'quality with missing governed required media')))->payload());

        self::assertFalse($payload['publishableQuality']);
        self::assertContains('requiredMediaCoverageReady', $payload['hardBlockers']);
        self::assertContains('bannerReady', $payload['advisoryWarnings']);
    }

    /**
     * @param array<string, mixed> $payload
     *
     * @return array{publishableQuality: bool, hardBlockers: list<string>, advisoryWarnings: list<string>}
     */
    private function normalizePayload(array $payload): array
    {
        return [
            'publishableQuality' => (bool) ($payload['publishableQuality'] ?? false),
            'hardBlockers' => $this->stringList($payload['hardBlockers'] ?? []),
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
