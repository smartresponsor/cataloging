<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Cataloging\Tests\Category;

use App\Cataloging\Entity\Catalog\CatalogSyndicationDeliveryRecordEntity;
use App\Cataloging\Policy\CategorySyndicationRetryPolicy;
use App\Cataloging\Service\CatalogSyndicationRetryService;
use App\Cataloging\ValueObject\CategorySyndicationDeliveryStatus;
use PHPUnit\Framework\TestCase;

final class CatalogSyndicationRetryServiceTest extends TestCase
{
    public function testPrepareRecoveryCandidateReturnsRetryablePayload(): void
    {
        $service = new CatalogSyndicationRetryService(new CategorySyndicationRetryPolicy());
        $record = new CatalogSyndicationDeliveryRecordEntity(
            'delivery-11',
            'package-11',
            'destination-11',
            'category-11',
            new CategorySyndicationDeliveryStatus('failed'),
            1,
            503,
            'temporary outage',
            null,
        );

        $event = $service->prepareRecoveryCandidate($record, 'operator-1', 'prepare recovery candidate');
        $payload = $event->payload();

        self::assertSame('delivery-11', $payload['deliveryId']);
        self::assertTrue($payload['retryable']);
        self::assertSame(503, $payload['responseCode']);
    }

    public function testScheduleRetryReturnsNextAttemptPlan(): void
    {
        $service = new CatalogSyndicationRetryService(new CategorySyndicationRetryPolicy());
        $record = new CatalogSyndicationDeliveryRecordEntity(
            'delivery-12',
            'package-12',
            'destination-12',
            'category-12',
            new CategorySyndicationDeliveryStatus('failed'),
            1,
            503,
            'temporary outage',
            null,
        );

        $event = $service->scheduleRetry($record, 'operator-1', 'schedule retry');
        $payload = $event->payload();

        self::assertSame(2, $payload['nextAttempt']);
        self::assertSame(900, $payload['delaySeconds']);
        self::assertTrue($payload['retryable']);
    }
}
