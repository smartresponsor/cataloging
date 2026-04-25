<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Cataloging\Tests\Category;

use App\Cataloging\Policy\CatalogCategoryWorkflowEntityPolicy;
use App\Cataloging\Policy\CategoryChangeRequestPolicy;
use App\Cataloging\Policy\CategoryPublicationGatePolicy;
use App\Cataloging\Repository\CatalogCategoryWorkflowEntityRepository;
use App\Cataloging\Repository\CategoryChangeRequestRepository;
use App\Cataloging\Service\CatalogChangeRequestService;
use App\Cataloging\Service\CatalogPublicationGateService;
use App\Cataloging\Service\CatalogReviewDecisionCouplingService;
use App\Cataloging\Service\CatalogWorkflowTransitionService;
use App\Cataloging\ValueObject\CategoryChangeRequestReviewRequest;
use App\Cataloging\ValueObject\CategoryChangeRequestSubmitRequest;
use App\Cataloging\ValueObject\CategoryReviewDecisionCouplingRequest;
use PHPUnit\Framework\TestCase;

final class CatalogReviewDecisionCouplingServiceTest extends TestCase
{
    public function testAcceptedReviewTransitionsWorkflowAndEvaluatesReadiness(): void
    {
        $changeRequestRepository = new CategoryChangeRequestRepository();
        $workflowRepository = new CatalogCategoryWorkflowEntityRepository();

        $changeRequestService = new CatalogChangeRequestService($changeRequestRepository, new CategoryChangeRequestPolicy());
        $workflowService = new CatalogWorkflowTransitionService($workflowRepository, new CatalogCategoryWorkflowEntityPolicy());
        $publicationGateService = new CatalogPublicationGateService(new CategoryPublicationGatePolicy());

        $service = new CatalogReviewDecisionCouplingService(
            $changeRequestService,
            $workflowService,
            $publicationGateService,
        );

        $changeRequestService->submit(new CategoryChangeRequestSubmitRequest(
            'req-510',
            'category-510',
            'author-1',
            'Promote category to spring landing',
            ['slug' => 'spring-landing'],
        ));
        $changeRequestService->review(new CategoryChangeRequestReviewRequest('req-510', 'in_review', 'reviewer-0', 'Entered moderation'));

        $event = $service->couple(new CategoryReviewDecisionCouplingRequest(
            'req-510',
            'accepted',
            'reviewer-1',
            'Approved for publish readiness',
            [
                'slugReady' => true,
                'seoReady' => true,
                'contentReady' => true,
                'localeReady' => true,
                'mediaReady' => false,
                'slugHistoryReady' => true,
            ],
        ));

        $payload = $event->payload();

        self::assertSame('accepted', $payload['reviewState']);
        self::assertSame('approved', $payload['workflowState']);
        self::assertTrue($payload['publishable']);
        self::assertSame([], $payload['blockers']);
        self::assertSame(['mediaReady'], $payload['warnings']);
    }

    public function testRejectedReviewReturnsCategoryToDraftAndMarksNotPublishable(): void
    {
        $changeRequestRepository = new CategoryChangeRequestRepository();
        $workflowRepository = new CatalogCategoryWorkflowEntityRepository();

        $changeRequestService = new CatalogChangeRequestService($changeRequestRepository, new CategoryChangeRequestPolicy());
        $workflowService = new CatalogWorkflowTransitionService($workflowRepository, new CatalogCategoryWorkflowEntityPolicy());
        $publicationGateService = new CatalogPublicationGateService(new CategoryPublicationGatePolicy());

        $service = new CatalogReviewDecisionCouplingService(
            $changeRequestService,
            $workflowService,
            $publicationGateService,
        );

        $changeRequestService->submit(new CategoryChangeRequestSubmitRequest(
            'req-511',
            'category-511',
            'author-1',
            'Remove deprecated slugHistory',
            ['slugHistory' => 'old-path'],
        ));
        $changeRequestService->review(new CategoryChangeRequestReviewRequest('req-511', 'in_review', 'reviewer-0', 'Entered moderation'));

        $event = $service->couple(new CategoryReviewDecisionCouplingRequest(
            'req-511',
            'rejected',
            'reviewer-2',
            'Rejected due to incomplete content',
            [
                'slugReady' => false,
                'seoReady' => false,
                'contentReady' => false,
                'localeReady' => false,
            ],
        ));

        $payload = $event->payload();

        self::assertSame('rejected', $payload['reviewState']);
        self::assertSame('draft', $payload['workflowState']);
        self::assertFalse($payload['publishable']);
        self::assertSame(['review_rejected'], $payload['blockers']);
        self::assertSame([], $payload['warnings']);
    }
}
