<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Cataloging\Tests\Category;

use App\Cataloging\Policy\CategoryWorkflowPolicy;
use App\Cataloging\ValueObject\CategoryWorkflowState;
use PHPUnit\Framework\TestCase;

final class CategoryWorkflowPolicyTest extends TestCase
{
    public function testDraftCanMoveToReviewWithActorAndReason(): void
    {
        $policy = new CategoryWorkflowPolicy();

        self::assertTrue($policy->canTransition(
            CategoryWorkflowState::fromString(CategoryWorkflowState::DRAFT),
            CategoryWorkflowState::fromString(CategoryWorkflowState::IN_REVIEW),
            'operator-1',
            'ready for moderation',
        ));
    }

    public function testDraftCannotPublishDirectly(): void
    {
        $policy = new CategoryWorkflowPolicy();

        self::assertFalse($policy->canTransition(
            CategoryWorkflowState::fromString(CategoryWorkflowState::DRAFT),
            CategoryWorkflowState::fromString(CategoryWorkflowState::PUBLISHED),
            'operator-1',
            'trying to bypass review',
        ));
    }

    public function testTransitionRequiresNonEmptyReason(): void
    {
        $policy = new CategoryWorkflowPolicy();

        self::assertFalse($policy->canTransition(
            CategoryWorkflowState::fromString(CategoryWorkflowState::APPROVED),
            CategoryWorkflowState::fromString(CategoryWorkflowState::PUBLISHED),
            'operator-1',
            '   ',
        ));
    }
}
