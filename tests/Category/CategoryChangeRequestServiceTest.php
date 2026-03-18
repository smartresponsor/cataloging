<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Tests\Category;

use App\Policy\CategoryChangeRequestPolicy;
use App\Repository\CategoryChangeRequestRepository;
use App\Service\CategoryChangeRequestService;
use PHPUnit\Framework\TestCase;

final class CategoryChangeRequestServiceTest extends TestCase
{
    public function testSubmitPersistsProposedRequest(): void
    {
        $service = new CategoryChangeRequestService(new CategoryChangeRequestRepository(), new CategoryChangeRequestPolicy());

        $request = $service->submit(
            'req-200',
            'category-200',
            'author-1',
            'Retitle category and update alias',
            ['title' => 'Outdoor furniture', 'alias' => 'patio-furniture'],
        );

        self::assertSame('req-200', $request->requestId());
        self::assertSame('proposed', $request->state()->value());
        self::assertSame('author-1', $request->submittedBy());
    }

    public function testReviewAcceptsExistingRequest(): void
    {
        $repository = new CategoryChangeRequestRepository();
        $service = new CategoryChangeRequestService($repository, new CategoryChangeRequestPolicy());
        $service->submit(
            'req-201',
            'category-201',
            'author-1',
            'Approve category move',
            ['parentId' => 'summer'],
        );

        $event = $service->review(
            'req-201',
            'accepted',
            'moderator-1',
            'Validated against merchandising policy',
        );

        $payload = $event->payload();
        self::assertSame('accepted', $payload['toState']);
        self::assertSame('moderator-1', $payload['reviewedBy']);
        self::assertSame('req-201', $payload['requestId']);
    }

    public function testReviewFailsWhenRequestIsMissing(): void
    {
        $service = new CategoryChangeRequestService(new CategoryChangeRequestRepository(), new CategoryChangeRequestPolicy());

        $this->expectException(\DomainException::class);

        $service->review(
            'req-missing',
            'rejected',
            'moderator-1',
            'No request exists',
        );
    }
}
