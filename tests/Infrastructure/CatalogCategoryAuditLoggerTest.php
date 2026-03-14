<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Tests\Infrastructure;

use App\Infrastructure\CategoryAuditLogger;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class CatalogCategoryAuditLoggerTest extends TestCase
{
    public function testLog(): void
    {
        $logger = new class implements LoggerInterface {
            public array $records = [];

            public function emergency(\Stringable|string $message, array $context = []): void
            {
            }

            public function alert(\Stringable|string $message, array $context = []): void
            {
            }

            public function critical(\Stringable|string $message, array $context = []): void
            {
            }

            public function error(\Stringable|string $message, array $context = []): void
            {
            }

            public function warning(\Stringable|string $message, array $context = []): void
            {
            }

            public function notice(\Stringable|string $message, array $context = []): void
            {
            }

            public function info(\Stringable|string $message, array $context = []): void
            {
                $this->records[] = [$message, $context];
            }

            public function debug(\Stringable|string $message, array $context = []): void
            {
            }

            public function log($level, \Stringable|string $message, array $context = []): void
            {
            }
        };

        $audit = new CategoryAuditLogger($logger);
        $audit->log('category.move', ['id' => 1]);

        self::assertCount(1, $logger->records);
    }
}
