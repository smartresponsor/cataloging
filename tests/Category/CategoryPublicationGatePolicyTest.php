<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Cataloging\Tests\CategoryEntity;

use App\Cataloging\Policy\CategoryPublicationGatePolicy;
use App\Cataloging\ValueObject\CatalogCategoryWorkflowEntityState;
use App\Cataloging\ValueObject\CategoryPublicationReadiness;
use PHPUnit\Framework\TestCase;

final class CategoryPublicationGatePolicyTest extends TestCase
{
    public function testApprovedCategoryWithRequiredChecksCanPublish(): void
    {
        $policy = new CategoryPublicationGatePolicy();
        $readiness = CategoryPublicationReadiness::fromChecks([
            'slugReady' => true,
            'seoReady' => true,
            'contentReady' => true,
            'localeReady' => true,
            'mediaReady' => false,
        ]);

        self::assertTrue($policy->canPublish(
            CatalogCategoryWorkflowEntityState::fromString(CatalogCategoryWorkflowEntityState::APPROVED),
            $readiness,
            'operator-1',
            'ready for release',
        ));
    }

    public function testApprovedCategoryWithMissingSeoCannotPublish(): void
    {
        $policy = new CategoryPublicationGatePolicy();
        $readiness = CategoryPublicationReadiness::fromChecks([
            'slugReady' => true,
            'seoReady' => false,
            'contentReady' => true,
            'localeReady' => true,
        ]);

        self::assertFalse($policy->canPublish(
            CatalogCategoryWorkflowEntityState::fromString(CatalogCategoryWorkflowEntityState::APPROVED),
            $readiness,
            'operator-1',
            'trying to publish incomplete category',
        ));
    }

    public function testReviewStateCannotPublishEvenIfChecksAreReady(): void
    {
        $policy = new CategoryPublicationGatePolicy();
        $readiness = CategoryPublicationReadiness::fromChecks([
            'slugReady' => true,
            'seoReady' => true,
            'contentReady' => true,
            'localeReady' => true,
        ]);

        self::assertFalse($policy->canPublish(
            CatalogCategoryWorkflowEntityState::fromString(CatalogCategoryWorkflowEntityState::IN_REVIEW),
            $readiness,
            'operator-1',
            'approval still missing',
        ));
    }
}
