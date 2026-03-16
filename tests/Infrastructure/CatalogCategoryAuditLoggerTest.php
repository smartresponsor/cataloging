<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure;

use App\Infrastructure\CategoryAuditLogger;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class CatalogCategoryAuditLoggerTest extends TestCase
{
    public function testLog(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects(self::once())
            ->method('info')
            ->with('category.audit', self::callback(static function (array $context): bool {
                return ($context['action'] ?? null) === 'category.move' && ($context['id'] ?? null) === 1;
            }));

        $audit = new CategoryAuditLogger($logger);
        $audit->log('category.move', ['id' => 1]);
    }
}
