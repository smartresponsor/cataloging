<?php

declare(strict_types=1);

namespace App\Cataloging\Tests\CategoryEntity;

use App\Cataloging\Idempotency\CategoryIdempotencyStore;
use App\Cataloging\Policy\CatalogCategoryWorkflowEntityPolicy;
use App\Cataloging\Policy\CategoryPublicationGatePolicy;
use App\Cataloging\Service\CatalogCacheInvalidationRecorderService;
use App\Cataloging\Service\CatalogCategoryMutationService;
use App\Cataloging\Service\CatalogOutboxWriterService;
use App\Cataloging\Service\CatalogPublicationGateService;
use App\Cataloging\Tests\Support\CategoryDoctrineEntityManagerFactory;
use App\Cataloging\ValueObject\CategoryMutationMoveRequest;
use App\Cataloging\ValueObject\CategoryMutationPublishRequest;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;

final class CategoryMutationServiceTest extends TestCase
{
    private const ROOT_ID = '1';
    private const ELECTRONICS_ID = '2';
    private const PHONES_ID = '3';
    private const FASHION_ID = '4';
    private const ROOT_SLUG = '018f4f0e-5d8c-7a3c-a0d4-1bf3d8c6a301';
    private const ELECTRONICS_SLUG = '018f4f0e-5d8c-7a3c-a0d4-1bf3d8c6a302';
    private const PHONES_SLUG = '018f4f0e-5d8c-7a3c-a0d4-1bf3d8c6a303';
    private const FASHION_SLUG = '018f4f0e-5d8c-7a3c-a0d4-1bf3d8c6a304';
    private const DEEP_ROOT_ID = '1';
    private const DEEP_A_ID = '2';
    private const DEEP_B_ID = '3';
    private const DEEP_C_ID = '4';
    private const DEEP_D_ID = '5';
    private const DEEP_X_ID = '6';
    private const DEEP_ROOT_SLUG = '018f4f0e-5d8c-7a3c-a0d4-1bf3d8c6a401';
    private const DEEP_A_SLUG = '018f4f0e-5d8c-7a3c-a0d4-1bf3d8c6a402';
    private const DEEP_B_SLUG = '018f4f0e-5d8c-7a3c-a0d4-1bf3d8c6a403';
    private const DEEP_C_SLUG = '018f4f0e-5d8c-7a3c-a0d4-1bf3d8c6a404';
    private const DEEP_D_SLUG = '018f4f0e-5d8c-7a3c-a0d4-1bf3d8c6a405';
    private const DEEP_X_SLUG = '018f4f0e-5d8c-7a3c-a0d4-1bf3d8c6a406';

    public function testMoveRebasesSubtreeUpdatesParentAndWritesAuditAndOutbox(): void
    {
        $connection = $this->createConnection();
        $this->createSchema($connection);
        $this->seedCategoryTree($connection);

        $service = $this->createService($connection);
        $result = $service->move(new CategoryMutationMoveRequest(self::ELECTRONICS_ID, self::FASHION_ID, 'oleksandr', 'catalog', 'strict'));

        self::assertSame(self::ELECTRONICS_ID, $result['id']);
        self::assertSame(self::FASHION_ID, $result['newParentId']);
        self::assertSame(2, $result['changedCount']);
        self::assertCount(2, $result['redirects']);
        self::assertFalse($result['duplicate']);

        $category = $connection->fetchAssociative('SELECT parent_id, path, depth FROM category WHERE id = :id', ['id' => self::ELECTRONICS_ID]);
        self::assertIsArray($category);
        self::assertSame(4, (int) $category['parent_id']);
        self::assertSame(self::ROOT_SLUG.'.'.self::FASHION_SLUG.'.'.self::ELECTRONICS_SLUG, $category['path']);
        self::assertTrue(is_scalar($category['depth']));
        self::assertSame(2, (int) $category['depth']);

        $phones = $connection->fetchAssociative('SELECT path, depth FROM category WHERE id = :id', ['id' => self::PHONES_ID]);
        self::assertIsArray($phones);
        self::assertSame(self::ROOT_SLUG.'.'.self::FASHION_SLUG.'.'.self::ELECTRONICS_SLUG.'.'.self::PHONES_SLUG, $phones['path']);
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
            self::ELECTRONICS_ID,
            self::FASHION_ID,
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
            self::ELECTRONICS_ID,
            self::ROOT_ID,
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
            self::DEEP_A_ID,
            self::DEEP_X_ID,
            'oleksandr',
            'catalog',
            'strict',
        ));

        self::assertSame(4, $result['changedCount']);

        $deepest = $connection->fetchAssociative('SELECT path, depth FROM category WHERE id = :id', ['id' => self::DEEP_D_ID]);
        self::assertIsArray($deepest);
        self::assertSame(self::DEEP_ROOT_SLUG.'.'.self::DEEP_X_SLUG.'.'.self::DEEP_A_SLUG.'.'.self::DEEP_B_SLUG.'.'.self::DEEP_C_SLUG.'.'.self::DEEP_D_SLUG, $deepest['path']);
        self::assertTrue(is_scalar($deepest['depth']));
        self::assertSame(5, (int) $deepest['depth']);
    }

    public function testMoveDuplicateCommandDoesNotWriteSecondOutboxRow(): void
    {
        $connection = $this->createConnection();
        $this->createSchema($connection);
        $this->seedCategoryTree($connection);

        $service = $this->createService($connection);
        $first = $service->move(new CategoryMutationMoveRequest(self::ELECTRONICS_ID, self::FASHION_ID, 'oleksandr', 'catalog', 'strict', false, null, 'move-1', 'corr-1'));
        $second = $service->move(new CategoryMutationMoveRequest(self::ELECTRONICS_ID, self::FASHION_ID, 'oleksandr', 'catalog', 'strict', false, null, 'move-1', 'corr-1'));

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
        $connection->update('category', ['workflow_state' => 'approved'], ['id' => self::ELECTRONICS_ID]);

        $service = $this->createService($connection);
        $result = $service->publish(new CategoryMutationPublishRequest(self::ELECTRONICS_ID, true, [
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

        $row = $connection->fetchAssociative('SELECT workflow_state, published, published_at FROM category WHERE id = :id', ['id' => self::ELECTRONICS_ID]);
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
        $connection->update('category', ['workflow_state' => 'approved'], ['id' => self::ELECTRONICS_ID]);

        $service = $this->createService($connection);
        $checks = [
            'slugReady' => true,
            'seoReady' => true,
            'contentReady' => true,
            'localeReady' => true,
        ];

        $first = $service->publish(new CategoryMutationPublishRequest(self::ELECTRONICS_ID, true, $checks, 'oleksandr', 'manual publish', 'publish-1', 'corr-2'));
        $second = $service->publish(new CategoryMutationPublishRequest(self::ELECTRONICS_ID, true, $checks, 'oleksandr', 'manual publish', 'publish-1', 'corr-2'));

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
            new CatalogOutboxWriterService($entityManager),
            new CatalogCacheInvalidationRecorderService(),
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
        $connection->executeStatement('CREATE TABLE category (id INTEGER PRIMARY KEY, slug TEXT NOT NULL, nameEntity TEXT NOT NULL DEFAULT "", parent_id INTEGER DEFAULT NULL, depth INTEGER NOT NULL DEFAULT 0, path TEXT DEFAULT NULL, locale TEXT DEFAULT NULL, tenant TEXT DEFAULT "default", icon_url TEXT DEFAULT NULL, workflow_state TEXT NOT NULL DEFAULT "draft", published INTEGER NOT NULL DEFAULT 0, published_at TEXT DEFAULT NULL)');
        $connection->executeStatement('CREATE TABLE category_audit (id TEXT PRIMARY KEY, action TEXT NOT NULL, payload TEXT NOT NULL, created_at TEXT NOT NULL)');
        $connection->executeStatement('CREATE TABLE outbox (id TEXT PRIMARY KEY, type TEXT NOT NULL, payload TEXT NOT NULL, "key" TEXT NOT NULL UNIQUE, created_at TEXT NOT NULL, available_at TEXT DEFAULT NULL, attempts INTEGER NOT NULL DEFAULT 0, last_error TEXT DEFAULT NULL, dispatched_at TEXT DEFAULT NULL, processed_at TEXT DEFAULT NULL, dead_lettered_at TEXT DEFAULT NULL)');
        $connection->executeStatement('CREATE TABLE category_idempotency (idempotency_key TEXT PRIMARY KEY, operation TEXT NOT NULL, request_hash TEXT NOT NULL, created_at TEXT NOT NULL, expires_at TEXT NOT NULL, correlation_id TEXT DEFAULT NULL)');
        $connection->executeStatement('CREATE INDEX idx_category_idempotency_expiry ON category_idempotency (expires_at)');
    }

    private function seedCategoryTree(Connection $connection): void
    {
        $rows = [
            ['id' => 1, 'slug' => self::ROOT_SLUG, 'nameEntity' => 'Root', 'parent_id' => null, 'depth' => 0, 'path' => self::ROOT_SLUG],
            ['id' => 2, 'slug' => self::ELECTRONICS_SLUG, 'nameEntity' => 'Electronics', 'parent_id' => 1, 'depth' => 1, 'path' => self::ROOT_SLUG.'.'.self::ELECTRONICS_SLUG],
            ['id' => 3, 'slug' => self::PHONES_SLUG, 'nameEntity' => 'Phones', 'parent_id' => 2, 'depth' => 2, 'path' => self::ROOT_SLUG.'.'.self::ELECTRONICS_SLUG.'.'.self::PHONES_SLUG],
            ['id' => 4, 'slug' => self::FASHION_SLUG, 'nameEntity' => 'Fashion', 'parent_id' => 1, 'depth' => 1, 'path' => self::ROOT_SLUG.'.'.self::FASHION_SLUG],
        ];

        foreach ($rows as $row) {
            $connection->insert('category', $row);
        }
    }

    private function seedFromFixture(Connection $connection, string $fixtureFile): void
    {
        $payload = Yaml::parseFile(__DIR__.'/../../fixtures/CategoryEntity/'.$fixtureFile);
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

            /* @var array<string, mixed> $row */
            $connection->insert('category', $row);
        }
    }
}
