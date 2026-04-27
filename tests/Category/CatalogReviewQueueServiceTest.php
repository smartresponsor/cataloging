<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Cataloging\Tests\Category;

use App\Cataloging\Policy\CatalogCategoryReviewAssignmentEntityPolicy;
use App\Cataloging\Policy\CategoryChangeRequestPolicy;
use App\Cataloging\Repository\Catalog\CatalogCategoryChangeRequestRepository;
use App\Cataloging\Repository\Catalog\CatalogCategoryReviewAssignmentRepository;
use App\Cataloging\Service\CatalogChangeRequestService;
use App\Cataloging\Service\CatalogReviewAssignmentService;
use App\Cataloging\Service\CatalogReviewQueueService;
use App\Cataloging\ValueObject\CatalogCategoryReviewAssignmentEntityRequest;
use App\Cataloging\ValueObject\CategoryChangeRequestReviewRequest;
use App\Cataloging\ValueObject\CategoryChangeRequestSubmitRequest;
use App\Cataloging\ValueObject\CategoryReviewQueueRequest;
use PHPUnit\Framework\TestCase;

final class CatalogReviewQueueServiceTest extends TestCase
{
    public function testBuildsReadyQueueItemForAssignedReviewer(): void
    {
        $changeRepository = new CatalogCategoryChangeRequestRepository();
        $assignmentRepository = new CatalogCategoryReviewAssignmentRepository();
        $requestService = new CatalogChangeRequestService($changeRepository, new CategoryChangeRequestPolicy());
        $assignmentService = new CatalogReviewAssignmentService(
            $changeRepository,
            $assignmentRepository,
            new CatalogCategoryReviewAssignmentEntityPolicy(),
        );
        $queueService = new CatalogReviewQueueService($changeRepository, $assignmentRepository);

        $requestService->submit(new CategoryChangeRequestSubmitRequest(
            'req-410',
            'category-410',
            'author-1',
            'Adjust category placement',
            ['parentId' => 'garden'],
        ));
        $requestService->review(new CategoryChangeRequestReviewRequest('req-410', 'in_review', 'moderator-1', 'Opened for review'));
        $assignmentService->assign(new CatalogCategoryReviewAssignmentEntityRequest('req-410', 'reviewer-1', 'lead-1', 'urgent'));

        $queue = $queueService->queueForReviewer(new CategoryReviewQueueRequest('reviewer-1'));

        self::assertCount(1, $queue);
        self::assertSame('req-410', $queue[0]->requestId());
        self::assertTrue($queue[0]->readyForReview());
        self::assertSame('urgent', $queue[0]->priority());
        self::assertSame([], $queue[0]->readinessWarnings());
    }

    public function testFlagsNotStartedRequestAsNotReady(): void
    {
        $changeRepository = new CatalogCategoryChangeRequestRepository();
        $assignmentRepository = new CatalogCategoryReviewAssignmentRepository();
        $requestService = new CatalogChangeRequestService($changeRepository, new CategoryChangeRequestPolicy());
        $assignmentService = new CatalogReviewAssignmentService(
            $changeRepository,
            $assignmentRepository,
            new CatalogCategoryReviewAssignmentEntityPolicy(),
        );
        $queueService = new CatalogReviewQueueService($changeRepository, $assignmentRepository);

        $requestService->submit(new CategoryChangeRequestSubmitRequest(
            'req-411',
            'category-411',
            'author-1',
            'Update seasonal sorting',
            ['sort' => 'seasonal'],
        ));
        $assignmentService->assign(new CatalogCategoryReviewAssignmentEntityRequest('req-411', 'reviewer-1', 'lead-1', 'normal'));

        $queue = $queueService->queueForReviewer(new CategoryReviewQueueRequest('reviewer-1'));

        self::assertCount(1, $queue);
        self::assertFalse($queue[0]->readyForReview());
        self::assertContains('request_not_started', $queue[0]->readinessWarnings());
    }
}
