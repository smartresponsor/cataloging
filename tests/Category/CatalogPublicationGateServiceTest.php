<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Cataloging\Tests\Category;

use App\Cataloging\Policy\CategoryPublicationGatePolicy;
use App\Cataloging\Service\CatalogPublicationGateService;
use App\Cataloging\ValueObject\CategoryPublicationGateAssertionRequest;
use App\Cataloging\ValueObject\CategoryPublicationGateEvaluationRequest;
use App\Cataloging\ValueObject\CategoryWorkflowState;
use PHPUnit\Framework\TestCase;

final class CatalogPublicationGateServiceTest extends TestCase
{
    public function testEvaluateReturnsPublishablePayloadForApprovedReadyCategory(): void
    {
        $service = new CatalogPublicationGateService(new CategoryPublicationGatePolicy());

        $event = $service->evaluate(new CategoryPublicationGateEvaluationRequest(
            'category-200',
            CategoryWorkflowState::APPROVED,
            [
                'slugReady' => true,
                'seoReady' => true,
                'contentReady' => true,
                'localeReady' => true,
                'mediaReady' => false,
                'aliasReady' => false,
            ],
            'operator-1',
            'release candidate approved',
        ));

        $payload = $event->payload();
        self::assertTrue($payload['publishable']);
        self::assertSame([], $payload['blockers']);
        self::assertSame(['mediaReady', 'aliasReady'], $payload['warnings']);
        self::assertSame('approved', $payload['workflowState']);
    }

    public function testAssertCanPublishFailsForDraftState(): void
    {
        $service = new CatalogPublicationGateService(new CategoryPublicationGatePolicy());

        $this->expectException(\DomainException::class);

        $service->assertCanPublish(new CategoryPublicationGateAssertionRequest(
            CategoryWorkflowState::DRAFT,
            [
                'slugReady' => true,
                'seoReady' => true,
                'contentReady' => true,
                'localeReady' => true,
            ],
            'operator-1',
            'attempting premature publish',
        ));
    }

    public function testAssertCanPublishFailsForMissingRequiredChecks(): void
    {
        $service = new CatalogPublicationGateService(new CategoryPublicationGatePolicy());

        $this->expectException(\DomainException::class);

        $service->assertCanPublish(new CategoryPublicationGateAssertionRequest(
            CategoryWorkflowState::APPROVED,
            [
                'slugReady' => true,
                'seoReady' => false,
                'contentReady' => true,
                'localeReady' => true,
            ],
            'operator-1',
            'seo still incomplete',
        ));
    }
}
