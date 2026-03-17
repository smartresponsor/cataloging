<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Tests\Category\Command;

use App\Command\ExportCategoryCommand;
use App\ExporterInterface\CategoryNdjsonExporterInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class ExportCategoryCommandTest extends TestCase
{
    public function testCommandDelegatesToExporterWithCanonicalArguments(): void
    {
        $captured = [];
        $exporter = new class($captured) implements CategoryNdjsonExporterInterface {
            public function __construct(private array &$captured)
            {
            }

            public function export(string $taxonomyCode, string $path, string $locale): void
            {
                $this->captured = [$taxonomyCode, $path, $locale];
            }
        };

        $tester = new CommandTester(new ExportCategoryCommand($exporter));
        $status = $tester->execute(['taxonomy' => 'catalog', 'file' => '/tmp/export.ndjson', 'locale' => 'uk']);

        self::assertSame(0, $status);
        self::assertSame(['catalog', '/tmp/export.ndjson', 'uk'], $captured);
        self::assertStringContainsString('taxonomy=catalog', $tester->getDisplay());
        self::assertStringContainsString('locale=uk', $tester->getDisplay());
    }
}
