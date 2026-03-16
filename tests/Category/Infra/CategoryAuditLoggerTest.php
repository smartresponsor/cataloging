<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Tests\Category\Infra;

use App\Infrastructure\CategoryAuditLogger;
use PHPUnit\Framework\TestCase;

final class CategoryAuditLoggerTest extends TestCase
{
    public function testLog(): void
    {
        $logger = new class {
            public array $records = [];

            public function info(string $msg, array $context = []): void
            {
                $this->records[] = [$msg, $context];
            }
        };
        $audit = new CategoryAuditLogger($logger);
        $audit->log('category.move', ['id' => 1]);
        $this->assertCount(1, $logger->records);
    }
}
