<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Cataloging\Tests\CategoryEntity;

use App\Cataloging\Policy\CatalogCategoryWorkflowEntityPolicy;
use App\Cataloging\ValueObject\CatalogCategoryWorkflowEntityState;
use PHPUnit\Framework\TestCase;

final class CategoryWorkflowPolicyTest extends TestCase
{
    public function testDraftCanMoveToReviewWithActorAndReason(): void
    {
        $policy = new CatalogCategoryWorkflowEntityPolicy();

        self::assertTrue($policy->canTransition(
            CatalogCategoryWorkflowEntityState::fromString(CatalogCategoryWorkflowEntityState::DRAFT),
            CatalogCategoryWorkflowEntityState::fromString(CatalogCategoryWorkflowEntityState::IN_REVIEW),
            'operator-1',
            'ready for moderation',
        ));
    }

    public function testDraftCannotPublishDirectly(): void
    {
        $policy = new CatalogCategoryWorkflowEntityPolicy();

        self::assertFalse($policy->canTransition(
            CatalogCategoryWorkflowEntityState::fromString(CatalogCategoryWorkflowEntityState::DRAFT),
            CatalogCategoryWorkflowEntityState::fromString(CatalogCategoryWorkflowEntityState::PUBLISHED),
            'operator-1',
            'trying to bypass review',
        ));
    }

    public function testTransitionRequiresNonEmptyReason(): void
    {
        $policy = new CatalogCategoryWorkflowEntityPolicy();

        self::assertFalse($policy->canTransition(
            CatalogCategoryWorkflowEntityState::fromString(CatalogCategoryWorkflowEntityState::APPROVED),
            CatalogCategoryWorkflowEntityState::fromString(CatalogCategoryWorkflowEntityState::PUBLISHED),
            'operator-1',
            '   ',
        ));
    }
}
