<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Tests\Category;

use App\Policy\CategoryCompletenessPolicy;
use App\Policy\CategoryPublicationGatePolicy;
use App\Service\CategoryCompletenessService;
use App\Service\CategoryPublicationGateService;
use App\ValueObject\CategoryWorkflowState;
use PHPUnit\Framework\TestCase;

final class CategoryPublicationCompletenessBridgeTest extends TestCase
{
    public function testCompletenessPublicationChecksCanDrivePublicationGateEvaluation(): void
    {
        $completeness = new CategoryCompletenessService(new CategoryCompletenessPolicy());
        $gate = new CategoryPublicationGateService(new CategoryPublicationGatePolicy());

        $completenessEvent = $completeness->evaluate('category-603', [
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
            'aliases' => [],
            'presentation' => [
                'bannerId' => '',
                'htmlBlockId' => '',
            ],
        ], 'operator-3', 'approval handoff');

        $gateEvent = $gate->evaluate(
            'category-603',
            CategoryWorkflowState::APPROVED,
            $completenessEvent->payload()['publicationChecks'],
            'operator-3',
            'approval handoff',
        );

        $payload = $gateEvent->payload();
        self::assertTrue($payload['publishable']);
        self::assertSame(['mediaReady', 'aliasReady'], $payload['warnings']);
    }
}
