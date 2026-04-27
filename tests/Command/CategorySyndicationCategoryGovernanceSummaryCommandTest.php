<?php

declare(strict_types=1);
/*
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>
 * Owner: Marketing America Corp
 */

namespace App\Cataloging\Tests\Command;

use App\Cataloging\Command\CategorySyndicationCategoryGovernanceSummaryCommand;
use App\Cataloging\Event\Catalog\CatalogCategorySyndicationCategoryGovernanceSummaryBuiltEvent;
use App\Cataloging\Service\ArrayValueNormalizer;
use App\Cataloging\ServiceInterface\CatalogSyndicationGovernanceSummaryServiceInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class CategorySyndicationCategoryGovernanceSummaryCommandTest extends TestCase
{
    public function testExecutePrintsCategoryGovernanceSummary(): void
    {
        $service = $this->createMock(CatalogSyndicationGovernanceSummaryServiceInterface::class);
        $service->method('buildSummary')->willReturn(new CatalogCategorySyndicationCategoryGovernanceSummaryBuiltEvent([
            'categoryId' => 'cat-1',
            'totalTrails' => 3,
            'destinationIds' => ['dest-1', 'dest-2'],
        ], new \DateTimeImmutable()));

        $tester = new CommandTester(new CategorySyndicationCategoryGovernanceSummaryCommand($service, new ArrayValueNormalizer()));
        $exitCode = $tester->execute([
            'categoryId' => 'cat-1',
            'actorId' => 'ops',
            'reason' => 'weekly-report',
            '--trails' => '[{"categoryId":"cat-1","destinationId":"dest-1"}]',
        ]);

        self::assertSame(0, $exitCode);
        self::assertStringContainsString('"categoryId": "cat-1"', $tester->getDisplay());
    }
}
