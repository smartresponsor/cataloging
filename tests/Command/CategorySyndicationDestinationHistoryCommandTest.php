<?php

declare(strict_types=1);
/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>
 * Owner: Marketing America Corp
 */

namespace App\Cataloging\Tests\Command;

use App\Cataloging\Command\CategorySyndicationDestinationHistoryCommand;
use App\Cataloging\Event\CategorySyndicationDestinationHistoryBuilt;
use App\Cataloging\ServiceInterface\CatalogSyndicationHistoryServiceInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class CategorySyndicationDestinationHistoryCommandTest extends TestCase
{
    public function testExecutePrintsHistorySummary(): void
    {
        $service = $this->createMock(CatalogSyndicationHistoryServiceInterface::class);
        $service->method('buildDestinationHistory')->willReturn(new CategorySyndicationDestinationHistoryBuilt([
            'destinationId' => 'dest-1', 'totalRecords' => 2, 'failedCount' => 1,
        ], new \DateTimeImmutable()));

        $tester = new CommandTester(new CategorySyndicationDestinationHistoryCommand($service));
        $tester->execute([
            'destinationId' => 'dest-1',
            'actorId' => 'ops',
            'reason' => 'inspect',
            '--records' => '[{"deliveryId":"d-1","packageId":"p-1","destinationId":"dest-1","categoryId":"cat-1","status":"failed","attempt":1}]',
        ]);

        self::assertStringContainsString('"destinationId": "dest-1"', $tester->getDisplay());
    }
}
