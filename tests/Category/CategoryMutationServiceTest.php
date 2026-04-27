<?php

declare(strict_types=1);

namespace App\Cataloging\Tests\Category;

use App\Cataloging\Idempotency\CategoryIdempotencyStore;
use App\Cataloging\Policy\CatalogCategoryWorkflowEntityPolicy;
use App\Cataloging\Policy\CategoryPublicationGatePolicy;
use App\Cataloging\Service\CacheInvalidationRecorder;
use App\Cataloging\Service\CatalogCategoryMutationService;
use App\Cataloging\Service\CatalogPublicationGateService;
use App\Cataloging\Service\OutboxWriter;
use App\Cataloging\Tests\Support\CategoryDoctrineEntityManagerFactory;
use App\Cataloging\ValueObject\CategoryMutationMoveRequest;
use App\Cataloging\ValueObject\CategoryMutationPublishRequest;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

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

        $category = $connection->fetchAssociative('SELECT parent_id, path, depth FROM category WHERE id = :id', ['id' => 'electronics']);
        self::assertIsArray($category);
        self::assertSame('fashion', $category['parent_id']);
        self::assertSame('root.fashion.electronics', $category['path']);
        self::assertTrue(is_scalar($category['depth']));
        self::assertSame(2, (int) $category['depth']);

        $phones = $connection->fetchAssociative('SELECT path, depth FROM category WHERE id = :id', ['id' => 'phones']);
        self::assertIsArray($phones);
        self::assertSame('root.fashion.electronics.phones', $phones['path']);
        self::assertTrue(is_scalar($phones['depth']));
        self::assertSame(3, (int) $phones['depth']);

        $moveAuditCount = $connection->fetchOne('SELECT COUNT(*) FROM category_audit WHERE action = :action', ['action' => 'category.move']);
        self::assertTrue(is_scalar($moveAuditCount));
        self::assertSame(1, (int) $moveAuditCount);

        $moveOutboxCount = $connection->fetchOne('SELECT COUNT(*) FROM outbox WHERE type = :type', ['type' => 'category.moved']);
        self::assertTrue(is_scalar($moveOutboxCount));
        self::assertSame(1, (int) $moveOutboxCount);
    }

    public function testMoveIdempotencyKeyReuseWithDifferentPayloadThrowsDomainException(): void
    {
        $connection = $this->createConnection();
        $this->createSchema($connection);
        $this->seedFromFixture($connection, 'mutation_idempotency_replay.yaml');

        $service = $this->createService($connection);
        $service->move(new CategoryMutationMoveRequest(
            'electronics',
            'fashion',
            'oleksandr',
            'catalog',
            'strict',
            false,
            null,
            'move-reuse-key',
            'corr-1',
        ));

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('cannot be reused for a different request payload');

        $service->move(new CategoryMutationMoveRequest(
            'electronics',
            'root',
            'oleksandr',
            'catalog',
            'strict',
            false,
            null,
            'move-reuse-key',
            'corr-1',
        ));
    }

    public function testMoveRebasesDeepTreeLoadedFromFixture(): void
    {
        $connection = $this->createConnection();
        $this->createSchema($connection);
        $this->seedFromFixture($connection, 'mutation_deep_tree.yaml');

        $service = $this->createService($connection);
        $result = $service->move(new CategoryMutationMoveRequest(
            'a',
            'x',
            'oleksandr',
            'catalog',
            'strict',
        ));

        self::assertSame(4, $result['changedCount']);

        $deepest = $connection->fetchAssociative('SELECT path, depth FROM category WHERE id = :id', ['id' => 'd']);
        self::assertIsArray($deepest);
        self::assertSame('root.x.a.b.c.d', $deepest['path']);
        self::assertTrue(is_scalar($deepest['depth']));
        self::assertSame(5, (int) $deepest['depth']);
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
        self::assertSame(['mediaReady', 'slugHistoryReady'], $result['warnings']);
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
        return CategoryDoctrineEntityManagerFactory::createConnection();
    }

    private function createService(Connection $connection): CatalogCategoryMutationService
    {
        $entityManager = $this->createEntityManager($connection);

        return new CatalogCategoryMutationService(
            $entityManager,
            new OutboxWriter($entityManager),
            new CacheInvalidationRecorder(),
            new CatalogPublicationGateService(new CategoryPublicationGatePolicy()),
            new CatalogCategoryWorkflowEntityPolicy(),
            new CategoryIdempotencyStore($entityManager),
        );
    }

    private function createEntityManager(Connection $connection): EntityManagerInterface
    {
        return CategoryDoctrineEntityManagerFactory::createEntityManager($connection);
    }

    private function createSchema(Connection $connection): void
    {
        $connection->executeStatement('CREATE TABLE category (id TEXT PRIMARY KEY, slug TEXT NOT NULL, name TEXT NOT NULL DEFAULT "", parent_id TEXT DEFAULT NULL, depth INTEGER NOT NULL DEFAULT 0, path TEXT DEFAULT NULL, locale TEXT DEFAULT NULL, tenant TEXT DEFAULT "default", icon_url TEXT DEFAULT NULL, workflow_state TEXT NOT NULL DEFAULT "draft", published INTEGER NOT NULL DEFAULT 0, published_at TEXT DEFAULT NULL)');
        $connection->executeStatement('CREATE TABLE category_audit (id TEXT PRIMARY KEY, action TEXT NOT NULL, payload TEXT NOT NULL, created_at TEXT NOT NULL)');
        $connection->executeStatement('CREATE TABLE outbox (id TEXT PRIMARY KEY, type TEXT NOT NULL, payload TEXT NOT NULL, "key" TEXT NOT NULL UNIQUE, created_at TEXT NOT NULL, available_at TEXT DEFAULT NULL, attempts INTEGER NOT NULL DEFAULT 0, last_error TEXT DEFAULT NULL, dispatched_at TEXT DEFAULT NULL, processed_at TEXT DEFAULT NULL, dead_lettered_at TEXT DEFAULT NULL)');
        $connection->executeStatement('CREATE TABLE category_idempotency (idempotency_key TEXT PRIMARY KEY, operation TEXT NOT NULL, request_hash TEXT NOT NULL, created_at TEXT NOT NULL, expires_at TEXT NOT NULL, correlation_id TEXT DEFAULT NULL)');
        $connection->executeStatement('CREATE INDEX idx_category_idempotency_expiry ON category_idempotency (expires_at)');
    }

    private function seedCategoryTree(Connection $connection): void
    {
        $rows = [
            ['id' => 'root', 'slug' => 'root', 'name' => 'Root', 'parent_id' => null, 'depth' => 0, 'path' => 'root'],
            ['id' => 'electronics', 'slug' => 'electronics', 'name' => 'Electronics', 'parent_id' => 'root', 'depth' => 1, 'path' => 'root.electronics'],
            ['id' => 'phones', 'slug' => 'phones', 'name' => 'Phones', 'parent_id' => 'electronics', 'depth' => 2, 'path' => 'root.electronics.phones'],
            ['id' => 'fashion', 'slug' => 'fashion', 'name' => 'Fashion', 'parent_id' => 'root', 'depth' => 1, 'path' => 'root.fashion'],
        ];

        foreach ($rows as $row) {
            $connection->insert('category', $row);
        }
    }

    private function seedFromFixture(Connection $connection, string $fixtureFile): void
    {
        $payload = Yaml::parseFile(__DIR__.'/../../fixtures/Category/'.$fixtureFile);
        if (!is_array($payload)) {
            return;
        }

        $rawRows = $payload['rows'] ?? [];
        if (!is_array($rawRows)) {
            return;
        }

        foreach ($rawRows as $row) {
            if (!is_array($row)) {
                continue;
            }

            /** @var array<string, mixed> $row */
            $connection->insert('category', $row);
        }
    }
}
