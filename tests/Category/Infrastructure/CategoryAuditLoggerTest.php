<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Tests\Category\Infrastructure;

use App\Infrastructure\CategoryAuditLogger;
use PHPUnit\Framework\TestCase;
use Psr\Log\AbstractLogger;

final class CategoryAuditLoggerTest extends TestCase
{
    public function testLog(): void
    {
        $logger = new class extends AbstractLogger {
            public array $records = [];

            public function log($level, string|\Stringable $message, array $context = []): void
            {
                $this->records[] = [(string) $level, (string) $message, $context];
            }
        };
        $audit = new CategoryAuditLogger($logger);
        $audit->log('category.move', ['id' => 1]);
        $this->assertCount(1, $logger->records);
    }
}
