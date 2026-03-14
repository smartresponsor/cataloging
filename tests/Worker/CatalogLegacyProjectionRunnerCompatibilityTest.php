<?php

declare(strict_types=1);

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

namespace App\Tests\Worker;

use App\Observability\CatalogProjectionMetrics;
use App\Projection\CatalogProjectionRunner;
use App\Runner\CategoryProjectionLoopRunner as LegacyCatalogProjectionLoopRunner;
use PHPUnit\Framework\TestCase;

final class CatalogLegacyProjectionRunnerCompatibilityTest extends TestCase
{
    public function testLegacyRunnerDelegatesToCanonicalRunner(): void
    {
        $reportDir = dirname(__DIR__, 2).'/report';
        if (!is_dir($reportDir)) {
            mkdir($reportDir, 0775, true);
        }

        file_put_contents($reportDir.'/catalog-export-flat.json', json_encode([['id' => 'cat-1']]));

        $legacyRunner = new LegacyCatalogProjectionLoopRunner(
            new CatalogProjectionRunner(new CatalogProjectionMetrics())
        );

        $legacyRunner->run(1, 1);

        self::assertFileExists($reportDir.'/catalog-projection-run.json');
        $content = file_get_contents($reportDir.'/catalog-projection-run.json');
        self::assertIsString($content);
        self::assertStringContainsString('"processed": 1', $content);
    }
}
