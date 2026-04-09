<?php

declare(strict_types=1);

namespace App\Tests\Category;

use App\Idempotency\CategoryIdempotencyStore;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use PHPUnit\Framework\TestCase;

final class CategoryIdempotencyStoreTest extends TestCase
{
    public function testAcquireReturnsFalseForDuplicateRequestAndThrowsOnPayloadMismatch(): void
    {
        $connection = $this->createConnection();
        $this->createSchema($connection);
        $store = new CategoryIdempotencyStore($connection);

        self::assertTrue($store->acquire('key-1', 'category.move', 'hash-a', 3600, 'corr-1'));
        self::assertFalse($store->acquire('key-1', 'category.move', 'hash-a', 3600, 'corr-1'));

        $this->expectException(\DomainException::class);
        $store->acquire('key-1', 'category.move', 'hash-b', 3600, 'corr-2');
    }

    public function testPurgeExpiredDeletesExpiredRows(): void
    {
        $connection = $this->createConnection();
        $this->createSchema($connection);
        $store = new CategoryIdempotencyStore($connection);

        $connection->insert('category_idempotency', [
            'idempotency_key' => 'expired-1',
            'operation' => 'category.publish',
            'request_hash' => 'hash-x',
            'created_at' => '2025-01-01 00:00:00',
            'expires_at' => '2025-01-01 00:00:00',
            'correlation_id' => 'corr-x',
        ]);

        self::assertSame(1, $store->purgeExpired());
        $remaining = $connection->fetchOne('SELECT COUNT(*) FROM category_idempotency');
        self::assertTrue(is_scalar($remaining));
        self::assertSame(0, (int) $remaining);
    }

    private function createConnection(): Connection
    {
        return DriverManager::getConnection(['driver' => 'pdo_sqlite', 'memory' => true]);
    }

    private function createSchema(Connection $connection): void
    {
        $connection->executeStatement('CREATE TABLE category_idempotency (idempotency_key TEXT PRIMARY KEY, operation TEXT NOT NULL, request_hash TEXT NOT NULL, created_at TEXT NOT NULL, expires_at TEXT NOT NULL, correlation_id TEXT DEFAULT NULL)');
        $connection->executeStatement('CREATE INDEX idx_category_idempotency_expiry ON category_idempotency (expires_at)');
    }
}
