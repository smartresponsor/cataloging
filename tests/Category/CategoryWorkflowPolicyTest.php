<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Tests\Category;

use App\Policy\CategoryWorkflowPolicy;
use App\ValueObject\CatalogCategoryWorkflowState;
use PHPUnit\Framework\TestCase;

final class CategoryWorkflowPolicyTest extends TestCase
{
    public function testDraftCanMoveToReviewWithActorAndReason(): void
    {
        $policy = new CategoryWorkflowPolicy();

        self::assertTrue($policy->canTransition(
            CatalogCategoryWorkflowState::fromString(CatalogCategoryWorkflowState::DRAFT),
            CatalogCategoryWorkflowState::fromString(CatalogCategoryWorkflowState::IN_REVIEW),
            'operator-1',
            'ready for moderation',
        ));
    }

    public function testDraftCannotPublishDirectly(): void
    {
        $policy = new CategoryWorkflowPolicy();

        self::assertFalse($policy->canTransition(
            CatalogCategoryWorkflowState::fromString(CatalogCategoryWorkflowState::DRAFT),
            CatalogCategoryWorkflowState::fromString(CatalogCategoryWorkflowState::PUBLISHED),
            'operator-1',
            'trying to bypass review',
        ));
    }

    public function testTransitionRequiresNonEmptyReason(): void
    {
        $policy = new CategoryWorkflowPolicy();

        self::assertFalse($policy->canTransition(
            CatalogCategoryWorkflowState::fromString(CatalogCategoryWorkflowState::APPROVED),
            CatalogCategoryWorkflowState::fromString(CatalogCategoryWorkflowState::PUBLISHED),
            'operator-1',
            '   ',
        ));
    }
}
