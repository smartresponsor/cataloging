<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Tests\Category\Command;

use App\Command\ImportCategoryCommand;
use App\ImporterInterface\CategoryNdjsonImporterInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class ImportCategoryCommandTest extends TestCase
{
    public function testCommandUsesImporterAndReportsCounts(): void
    {
        $captured = [];
        $importer = new class($captured) implements CategoryNdjsonImporterInterface {
            public function __construct(private array &$captured)
            {
            }

            public function import(string $path, bool $dryRun = true): array
            {
                $this->captured = [$path, $dryRun];

                return ['ok' => 2, 'fail' => 0, 'warnings' => 1, 'report' => ['warn-line']];
            }
        };

        $tester = new CommandTester(new ImportCategoryCommand($importer));
        $status = $tester->execute(['file' => '/tmp/import.ndjson', '--dry-run' => true]);

        self::assertSame(0, $status);
        self::assertSame(['/tmp/import.ndjson', true], $captured);
        self::assertStringContainsString('ok=2 fail=0 warnings=1', $tester->getDisplay());
        self::assertStringContainsString('warn-line', $tester->getDisplay());
    }
}
