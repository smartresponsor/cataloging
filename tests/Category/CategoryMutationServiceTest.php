<?php

declare(strict_types=1);

namespace App\Tests\Category;

use App\Idempotency\CategoryIdempotencyStore;
use App\Policy\CategoryPublicationGatePolicy;
use App\Policy\CategoryWorkflowPolicy;
use App\Service\CacheInvalidationRecorder;
use App\Service\CatalogPublicationGateService;
use App\Service\CategoryMutationService;
use App\Service\OutboxWriter;
use App\ValueObject\CategoryMutationMoveRequest;
use App\ValueObject\CategoryMutationPublishRequest;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use PHPUnit\Framework\TestCase;

final class CategoryMutationServiceTest extends TestCase
{
    public function testMoveRebasesSubtreeUpdatesParentAndWritesAuditAndOutbox(): void
    {
        $connection = $this->createConnection();
        $this->createSchema($connection);
        $this->seedCategoryTree($connection);

        $service = $this->createService($connection);
        $result = $service->move(new CategoryMutationMoveRequest('electronics', 'fashion', 'oleksandr', 'catalog', 'strict'));

        self::assertSame('electronics', $result['id']);
        self::assertSame('fashion', $result['newParentId']);
        self::assertSame(2, $result['changedCount']);
        self::assertCount(2, $result['redirects']);
        self::assertFalse($result['duplicate']);

        $category = $connection->fetchAssociative('SELECT parent_id, path, level FROM category WHERE id = :id', ['id' => 'electronics']);
        self::assertIsArray($category);
        self::assertSame('fashion', $category['parent_id']);
        self::assertSame('root.fashion.electronics', $category['path']);
        self::assertTrue(is_scalar($category['level']));
        self::assertSame(2, (int) $category['level']);

        $phones = $connection->fetchAssociative('SELECT path, level FROM category WHERE id = :id', ['id' => 'phones']);
        self::assertIsArray($phones);
        self::assertSame('root.fashion.electronics.phones', $phones['path']);
        self::assertTrue(is_scalar($phones['level']));
        self::assertSame(3, (int) $phones['level']);

        $moveAuditCount = $connection->fetchOne('SELECT COUNT(*) FROM category_audit WHERE action = :action', ['action' => 'category.move']);
        self::assertTrue(is_scalar($moveAuditCount));
        self::assertSame(1, (int) $moveAuditCount);

        $moveOutboxCount = $connection->fetchOne('SELECT COUNT(*) FROM outbox WHERE type = :type', ['type' => 'category.moved']);
        self::assertTrue(is_scalar($moveOutboxCount));
        self::assertSame(1, (int) $moveOutboxCount);
    }

    public function testMoveDuplicateCommandDoesNotWriteSecondOutboxRow(): void
    {
        $connection = $this->createConnection();
        $this->createSchema($connection);
        $this->seedCategoryTree($connection);

        $service = $this->createService($connection);
        $first = $service->move(new CategoryMutationMoveRequest('electronics', 'fashion', 'oleksandr', 'catalog', 'strict', false, null, 'move-1', 'corr-1'));
        $second = $service->move(new CategoryMutationMoveRequest('electronics', 'fashion', 'oleksandr', 'catalog', 'strict', false, null, 'move-1', 'corr-1'));

        self::assertFalse($first['duplicate']);
        self::assertTrue($second['duplicate']);
        self::assertSame(0, $second['changedCount']);

        $moveAuditCount = $connection->fetchOne('SELECT COUNT(*) FROM category_audit WHERE action = :action', ['action' => 'category.move']);
        self::assertTrue(is_scalar($moveAuditCount));
        self::assertSame(1, (int) $moveAuditCount);

        $moveOutboxCount = $connection->fetchOne('SELECT COUNT(*) FROM outbox WHERE type = :type', ['type' => 'category.moved']);
        self::assertTrue(is_scalar($moveOutboxCount));
        self::assertSame(1, (int) $moveOutboxCount);
    }

    public function testPublishUpdatesPublicationStateAndWritesAuditAndOutbox(): void
    {
        $connection = $this->createConnection();
        $this->createSchema($connection);
        $this->seedCategoryTree($connection);
        $connection->update('category', ['workflow_state' => 'approved'], ['id' => 'electronics']);

        $service = $this->createService($connection);
        $result = $service->publish(new CategoryMutationPublishRequest('electronics', true, [
            'slugReady' => true,
            'seoReady' => true,
            'contentReady' => true,
            'localeReady' => true,
            'mediaReady' => false,
        ], 'oleksandr', 'manual publish'));

        self::assertTrue($result['published']);
        self::assertSame('published', $result['workflowState']);
        self::assertSame('approved', $result['previousWorkflowState']);
        self::assertSame(['mediaReady'], $result['warnings']);
        self::assertSame([], $result['blockers']);
        self::assertNotNull($result['publishedAt']);
        self::assertFalse($result['duplicate']);

        $row = $connection->fetchAssociative('SELECT workflow_state, published, published_at FROM category WHERE id = :id', ['id' => 'electronics']);
        self::assertIsArray($row);
        self::assertSame('published', $row['workflow_state']);
        self::assertTrue(is_scalar($row['published']));
        self::assertSame(1, (int) $row['published']);
        self::assertNotNull($row['published_at']);

        $publishAuditCount = $connection->fetchOne('SELECT COUNT(*) FROM category_audit WHERE action = :action', ['action' => 'category.publish']);
        self::assertTrue(is_scalar($publishAuditCount));
        self::assertSame(1, (int) $publishAuditCount);

        $publishOutboxCount = $connection->fetchOne('SELECT COUNT(*) FROM outbox WHERE type = :type', ['type' => 'category.published']);
        self::assertTrue(is_scalar($publishOutboxCount));
        self::assertSame(1, (int) $publishOutboxCount);
    }

    public function testPublishDuplicateCommandDoesNotWriteSecondOutboxRow(): void
    {
        $connection = $this->createConnection();
        $this->createSchema($connection);
        $this->seedCategoryTree($connection);
        $connection->update('category', ['workflow_state' => 'approved'], ['id' => 'electronics']);

        $service = $this->createService($connection);
        $checks = [
            'slugReady' => true,
            'seoReady' => true,
            'contentReady' => true,
            'localeReady' => true,
        ];

        $first = $service->publish(new CategoryMutationPublishRequest('electronics', true, $checks, 'oleksandr', 'manual publish', 'publish-1', 'corr-2'));
        $second = $service->publish(new CategoryMutationPublishRequest('electronics', true, $checks, 'oleksandr', 'manual publish', 'publish-1', 'corr-2'));

        self::assertFalse($first['duplicate']);
        self::assertTrue($second['duplicate']);
        self::assertTrue($second['published']);
        self::assertSame('published', $second['workflowState']);

        $publishAuditCount = $connection->fetchOne('SELECT COUNT(*) FROM category_audit WHERE action = :action', ['action' => 'category.publish']);
        self::assertTrue(is_scalar($publishAuditCount));
        self::assertSame(1, (int) $publishAuditCount);

        $publishOutboxCount = $connection->fetchOne('SELECT COUNT(*) FROM outbox WHERE type = :type', ['type' => 'category.published']);
        self::assertTrue(is_scalar($publishOutboxCount));
        self::assertSame(1, (int) $publishOutboxCount);
    }

    private function createConnection(): Connection
    {
        return DriverManager::getConnection(['driver' => 'pdo_sqlite', 'memory' => true]);
    }

    private function createService(Connection $connection): CategoryMutationService
    {
        return new CategoryMutationService(
            $connection,
            new OutboxWriter($connection),
            new CacheInvalidationRecorder(),
            new CatalogPublicationGateService(new CategoryPublicationGatePolicy()),
            new CategoryWorkflowPolicy(),
            new CategoryIdempotencyStore($connection),
        );
    }

    private function createSchema(Connection $connection): void
    {
        $connection->executeStatement('CREATE TABLE category (id TEXT PRIMARY KEY, slug TEXT NOT NULL, name TEXT NOT NULL DEFAULT "", parent_id TEXT DEFAULT NULL, level INTEGER NOT NULL DEFAULT 0, path TEXT DEFAULT NULL, locale TEXT DEFAULT NULL, tenant TEXT DEFAULT "default", icon_url TEXT DEFAULT NULL, workflow_state TEXT NOT NULL DEFAULT "draft", published INTEGER NOT NULL DEFAULT 0, published_at TEXT DEFAULT NULL)');
        $connection->executeStatement('CREATE TABLE category_audit (id TEXT PRIMARY KEY, action TEXT NOT NULL, payload TEXT NOT NULL, created_at TEXT NOT NULL)');
        $connection->executeStatement('CREATE TABLE outbox (id TEXT PRIMARY KEY, type TEXT NOT NULL, payload TEXT NOT NULL, "key" TEXT NOT NULL UNIQUE, created_at TEXT NOT NULL, processed_at TEXT DEFAULT NULL)');
        $connection->executeStatement('CREATE TABLE category_idempotency (idempotency_key TEXT PRIMARY KEY, operation TEXT NOT NULL, request_hash TEXT NOT NULL, created_at TEXT NOT NULL, expires_at TEXT NOT NULL, correlation_id TEXT DEFAULT NULL)');
        $connection->executeStatement('CREATE INDEX idx_category_idempotency_expiry ON category_idempotency (expires_at)');
    }

    private function seedCategoryTree(Connection $connection): void
    {
        $rows = [
            ['id' => 'root', 'slug' => 'root', 'name' => 'Root', 'parent_id' => null, 'level' => 0, 'path' => 'root'],
            ['id' => 'electronics', 'slug' => 'electronics', 'name' => 'Electronics', 'parent_id' => 'root', 'level' => 1, 'path' => 'root.electronics'],
            ['id' => 'phones', 'slug' => 'phones', 'name' => 'Phones', 'parent_id' => 'electronics', 'level' => 2, 'path' => 'root.electronics.phones'],
            ['id' => 'fashion', 'slug' => 'fashion', 'name' => 'Fashion', 'parent_id' => 'root', 'level' => 1, 'path' => 'root.fashion'],
        ];

        foreach ($rows as $row) {
            $connection->insert('category', $row);
        }
    }
}
