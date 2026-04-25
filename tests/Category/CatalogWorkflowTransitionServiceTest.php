<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Cataloging\Tests\Category;

use App\Cataloging\Policy\CatalogCategoryWorkflowEntityPolicy;
use App\Cataloging\Repository\CatalogCategoryWorkflowEntityRepository;
use App\Cataloging\Service\CatalogWorkflowTransitionService;
use App\Cataloging\ValueObject\CatalogCategoryWorkflowEntityState;
use App\Cataloging\ValueObject\CatalogCategoryWorkflowEntityTransitionRequest;
use PHPUnit\Framework\TestCase;

final class CatalogWorkflowTransitionServiceTest extends TestCase
{
    public function testTransitionStoresCurrentStateAndAppendsAuditEvent(): void
    {
        $repository = new CatalogCategoryWorkflowEntityRepository();
        $service = new CatalogWorkflowTransitionService($repository, new CatalogCategoryWorkflowEntityPolicy());

        $event = $service->transition(new CatalogCategoryWorkflowEntityTransitionRequest(
            'category-100',
            CatalogCategoryWorkflowEntityState::IN_REVIEW,
            'operator-1',
            'ready for moderation',
        ));

        $current = $repository->findByCategoryId('category-100');
        self::assertNotNull($current);
        self::assertSame(CatalogCategoryWorkflowEntityState::IN_REVIEW, $current->state()->value());
        self::assertSame('operator-1', $current->actorId());
        self::assertSame('ready for moderation', $current->reason());

        $payload = $event->payload();
        self::assertSame('draft', $payload['fromState']);
        self::assertSame('in_review', $payload['toState']);

        $history = $repository->historyForCategoryId('category-100');
        self::assertCount(1, $history);
        self::assertSame('category-100', $history[0]->payload()['categoryId']);
    }

    public function testTransitionRejectsInvalidStateJump(): void
    {
        $repository = new CatalogCategoryWorkflowEntityRepository();
        $service = new CatalogWorkflowTransitionService($repository, new CatalogCategoryWorkflowEntityPolicy());

        $this->expectException(\DomainException::class);

        $service->transition(new CatalogCategoryWorkflowEntityTransitionRequest(
            'category-101',
            CatalogCategoryWorkflowEntityState::PUBLISHED,
            'operator-1',
            'skipping review',
        ));
    }
}
