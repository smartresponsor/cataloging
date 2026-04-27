<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Cataloging\Tests\Category;

use App\Cataloging\Policy\CatalogSyndicationDestinationPolicy;
use App\Cataloging\Repository\Catalog\CatalogSyndicationDestinationRepository;
use App\Cataloging\Service\CatalogSyndicationDestinationService;
use App\Cataloging\ValueObject\CatalogAuditContext;
use App\Cataloging\ValueObject\CatalogSyndicationDestinationConfiguration;
use App\Cataloging\ValueObject\CatalogSyndicationDestinationDefinition;
use App\Cataloging\ValueObject\CatalogSyndicationDestinationRegisterRequest;
use PHPUnit\Framework\TestCase;

final class CatalogSyndicationDestinationServiceTest extends TestCase
{
    public function testRegisterBuildsDestinationEventPayload(): void
    {
        $service = new CatalogSyndicationDestinationService(
            new CatalogSyndicationDestinationPolicy(),
            new CatalogSyndicationDestinationRepository(),
        );

        $event = $service->register(
            new CatalogSyndicationDestinationRegisterRequest(
                new CatalogSyndicationDestinationDefinition(
                    'destination-01',
                    'US Search Feed',
                    'search',
                    'export',
                ),
                new CatalogSyndicationDestinationConfiguration(
                    true,
                    [
                        'feedCode' => 'search-us',
                        'channel' => 'web-us',
                    ],
                ),
                new CatalogAuditContext(
                    'operator-7',
                    'baseline syndication destination registration',
                ),
            ),
        );

        $payload = $event->payload();
        self::assertSame('destination-01', $payload['destinationId']);
        self::assertSame('search', $payload['destinationType']);
        self::assertSame('export', $payload['deliveryMode']);
        self::assertTrue($payload['enabled']);
        self::assertSame('operator-7', $payload['actorId']);
        self::assertSame('baseline syndication destination registration', $payload['reason']);
    }
}
