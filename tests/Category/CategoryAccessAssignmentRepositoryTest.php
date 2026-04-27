<?php

declare(strict_types=1);

namespace App\Cataloging\Tests\Category;

use App\Cataloging\Entity\CatalogCategoryAccessAssignmentEntity;
use App\Cataloging\Repository\Catalog\CatalogCategoryAccessAssignmentRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use PHPUnit\Framework\TestCase;

final class CategoryAccessAssignmentRepositoryTest extends TestCase
{
    public function testSaveAndLookupRoundTripUsingDbConnection(): void
    {
        $connection = DriverManager::getConnection(['driver' => 'pdo_sqlite', 'memory' => true]);
        $this->createSchema($connection);

        $repository = new CatalogCategoryAccessAssignmentRepository($connection);
        $assignment = CatalogCategoryAccessAssignmentEntity::create('category-1', 'oleksandr', 'owner', true);
        $repository->save($assignment);

        $found = $repository->findOneByCategoryIdAndActorUserId('category-1', 'oleksandr');

        self::assertNotNull($found);
        self::assertSame('owner', $found->role());
        self::assertTrue($found->isPrimary());
        self::assertCount(1, $repository->findActiveByCategoryId('category-1'));
        self::assertCount(1, $repository->findActiveByActorUserId('oleksandr'));
    }

    private function createSchema(Connection $connection): void
    {
        $connection->executeStatement('CREATE TABLE category_access_assignment (assignment_id TEXT PRIMARY KEY, category_id TEXT NOT NULL, actor_user_id TEXT NOT NULL, role TEXT NOT NULL, status TEXT NOT NULL, is_primary INTEGER NOT NULL DEFAULT 0, granted_at TEXT NOT NULL, revoked_at TEXT DEFAULT NULL)');
        $connection->executeStatement('CREATE UNIQUE INDEX uniq_category_access_assignment_actor ON category_access_assignment (category_id, actor_user_id)');
        $connection->executeStatement('CREATE INDEX idx_category_access_assignment_category_status ON category_access_assignment (category_id, status)');
        $connection->executeStatement('CREATE INDEX idx_category_access_assignment_actor_status ON category_access_assignment (actor_user_id, status)');
    }
}
