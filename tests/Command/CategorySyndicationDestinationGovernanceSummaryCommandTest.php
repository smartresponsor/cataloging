<?php

declare(strict_types=1);
/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>
 * Owner: Marketing America Corp
 */

namespace App\Cataloging\Tests\Command;

use App\Cataloging\Command\CatalogSyndicationDestinationGovernanceSummaryCommand;
use App\Cataloging\Event\CatalogSyndicationDestinationGovernanceSummaryBuilt;
use App\Cataloging\ServiceInterface\CatalogSyndicationDestinationGovernanceSummaryServiceInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class CategorySyndicationDestinationGovernanceSummaryCommandTest extends TestCase
{
    public function testExecutePrintsDestinationGovernanceSummary(): void
    {
        $service = $this->createMock(CatalogSyndicationDestinationGovernanceSummaryServiceInterface::class);
        $service->method('buildSummary')->willReturn(new CatalogSyndicationDestinationGovernanceSummaryBuilt([
            'destinationId' => 'dest-1',
            'totalTrails' => 2,
            'resolvedPublishableCount' => 1,
        ], new \DateTimeImmutable()));

        $tester = new CommandTester(new CatalogSyndicationDestinationGovernanceSummaryCommand($service));
        $exitCode = $tester->execute([
            'destinationId' => 'dest-1',
            'actorId' => 'ops',
            'reason' => 'daily-check',
            '--trails' => '[{"destinationId":"dest-1","resolvedPublishable":true}]',
        ]);

        self::assertSame(0, $exitCode);
        self::assertStringContainsString('"destinationId": "dest-1"', $tester->getDisplay());
    }
}
