<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Tests\Category\Projection;

use App\Observability\CatalogProjectionMetrics;
use App\Projection\CategoryProjectionRunner;
use PHPUnit\Framework\TestCase;

final class CategoryProjectionRunnerTest extends TestCase
{
    public function testRunOnceResetsLagMetricToZero(): void
    {
        $metrics = new CatalogProjectionMetrics();
        $metrics->setLag(37);

        $runner = new CategoryProjectionRunner($metrics);
        $runner->runOnce();

        self::assertSame(0, $metrics->getLag());
    }
}
