<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Cataloging\Tests\CategoryEntity;

use App\Cataloging\Policy\CategoryCompletenessPolicy;
use App\Cataloging\Policy\CategoryPublicationGatePolicy;
use App\Cataloging\Service\CatalogCompletenessService;
use App\Cataloging\Service\CatalogPublicationGateService;
use App\Cataloging\ValueObject\CatalogAuditContext;
use App\Cataloging\ValueObject\CatalogCategoryWorkflowEntityState;
use App\Cataloging\ValueObject\CategoryEvaluationRequest;
use App\Cataloging\ValueObject\CategoryPublicationGateEvaluationRequest;
use PHPUnit\Framework\TestCase;

final class CategoryPublicationCompletenessBridgeTest extends TestCase
{
    public function testCompletenessPublicationChecksCanDrivePublicationGateEvaluation(): void
    {
        $completeness = new CatalogCompletenessService(new CategoryCompletenessPolicy());
        $gate = new CatalogPublicationGateService(new CategoryPublicationGatePolicy());

        $completenessEvent = $completeness->evaluate(new CategoryEvaluationRequest('category-603', [
            'slug' => 'chairs',
            'seo' => [
                'title' => 'Chairs',
                'description' => 'Browse chairs',
            ],
            'content' => [
                'body' => 'Curated category copy',
            ],
            'locale' => [
                'enabled' => ['en_US', 'uk_UA'],
            ],
            'media' => [
                'primaryAssetId' => '',
            ],
            'slugHistories' => [],
            'presentation' => [
                'bannerId' => '',
                'htmlBlockId' => '',
            ],
        ], new CatalogAuditContext('operator-3', 'approval handoff')));

        /** @var array{publicationChecks:array<string,bool>} $completenessPayload */
        $completenessPayload = $completenessEvent->payload();
        $gateEvent = $gate->evaluate(new CategoryPublicationGateEvaluationRequest(
            'category-603',
            CatalogCategoryWorkflowEntityState::APPROVED,
            $completenessPayload['publicationChecks'],
            'operator-3',
            'approval handoff',
        ));

        /** @var array{publishable:bool,warnings:list<string>} $payload */
        $payload = $gateEvent->payload();
        self::assertTrue($payload['publishable']);
        self::assertSame(['mediaReady', 'slugHistoryReady'], $payload['warnings']);
    }
}
