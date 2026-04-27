<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Cataloging\Tests\Category;

use App\Cataloging\Policy\CategoryChangeRequestPolicy;
use App\Cataloging\Repository\Catalog\CatalogCategoryChangeRequestRepository;
use App\Cataloging\Service\CatalogChangeRequestService;
use App\Cataloging\ValueObject\CategoryChangeRequestReviewRequest;
use App\Cataloging\ValueObject\CategoryChangeRequestSubmitRequest;
use PHPUnit\Framework\TestCase;

final class CatalogChangeRequestServiceTest extends TestCase
{
    public function testSubmitPersistsProposedRequest(): void
    {
        $service = new CatalogChangeRequestService(new CatalogCategoryChangeRequestRepository(), new CategoryChangeRequestPolicy());

        $request = $service->submit(new CategoryChangeRequestSubmitRequest(
            'req-200',
            'category-200',
            'author-1',
            'Retitle category and update slugHistory',
            ['title' => 'Outdoor furniture', 'slugHistory' => 'patio-furniture'],
        ));

        self::assertSame('req-200', $request->requestId());
        self::assertSame('proposed', $request->state()->value());
        self::assertSame('author-1', $request->submittedBy());
    }

    public function testReviewAcceptsExistingRequest(): void
    {
        $repository = new CatalogCategoryChangeRequestRepository();
        $service = new CatalogChangeRequestService($repository, new CategoryChangeRequestPolicy());
        $service->submit(new CategoryChangeRequestSubmitRequest(
            'req-201',
            'category-201',
            'author-1',
            'Approve category move',
            ['parentId' => 'summer'],
        ));

        $event = $service->review(new CategoryChangeRequestReviewRequest(
            'req-201',
            'accepted',
            'moderator-1',
            'Validated against merchandising policy',
        ));

        $payload = $event->payload();
        self::assertSame('accepted', $payload['toState']);
        self::assertSame('moderator-1', $payload['reviewedBy']);
        self::assertSame('req-201', $payload['requestId']);
    }

    public function testReviewFailsWhenRequestIsMissing(): void
    {
        $service = new CatalogChangeRequestService(new CatalogCategoryChangeRequestRepository(), new CategoryChangeRequestPolicy());

        $this->expectException(\DomainException::class);

        $service->review(new CategoryChangeRequestReviewRequest(
            'req-missing',
            'rejected',
            'moderator-1',
            'No request exists',
        ));
    }
}
