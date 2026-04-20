<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Cataloging\Tests\Category;

use App\Cataloging\Policy\CategorySyndicationDeliveryPolicy;
use App\Cataloging\Repository\CategorySyndicationDeliveryRecordRepository;
use App\Cataloging\Service\CatalogSyndicationDeliveryService;
use App\Cataloging\ValueObject\CatalogAuditContext;
use App\Cataloging\ValueObject\CategorySyndicationDeliveryAttempt;
use App\Cataloging\ValueObject\CategorySyndicationDeliveryContext;
use App\Cataloging\ValueObject\CategorySyndicationDeliveryRecordRequest;
use PHPUnit\Framework\TestCase;

final class CatalogSyndicationDeliveryServiceTest extends TestCase
{
    public function testRecordDeliveryReturnsLedgerPayload(): void
    {
        $service = new CatalogSyndicationDeliveryService(
            new CategorySyndicationDeliveryPolicy(),
            new CategorySyndicationDeliveryRecordRepository(),
        );

        $event = $service->recordDelivery(
            new CategorySyndicationDeliveryRecordRequest(
                new CategorySyndicationDeliveryContext(
                    'delivery-100',
                    'pkg-100',
                    'dest-100',
                    'category-500',
                    'failed',
                ),
                new CategorySyndicationDeliveryAttempt(1, 503, 'temporary downstream outage'),
                new CatalogAuditContext('operator-1', 'record delivery result'),
            ),
        );

        $payload = $event->payload();
        self::assertSame('delivery-100', $payload['deliveryId']);
        self::assertSame('pkg-100', $payload['packageId']);
        self::assertSame('failed', $payload['status']);
        self::assertSame(503, $payload['responseCode']);
        self::assertTrue($payload['retryable']);
        self::assertNull($payload['deliveredAt']);
    }
}
