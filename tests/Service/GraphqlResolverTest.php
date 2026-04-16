<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Service\GraphqlResolver;
use App\ServiceInterface\CategoryProjectionReadServiceInterface;
use App\ValueObject\CategoryGraphqlNodeRequest;
use App\ValueObject\CategoryProjectionCriteria;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class GraphqlResolverTest extends TestCase
{
    public function testCategoryUsesProjectionRow(): void
    {
        $readService = new class implements CategoryProjectionReadServiceInterface {
            public function list(?CategoryProjectionCriteria $criteria = null): array
            {
                return [];
            }

            public function tree(?CategoryProjectionCriteria $criteria = null): array
            {
                return [];
            }

            public function findOne(string $id): array
            {
                return [
                    'id' => $id,
                    'parent_id' => null,
                    'slug' => 'root',
                    'name' => 'Root',
                    'locale' => 'en',
                    'tenant' => 'default',
                    'workflow_state' => 'published',
                    'published' => true,
                    'published_at' => '2025-11-02 00:00:00',
                    'updated_at' => '2025-11-02 00:00:00',
                    'path' => 'root',
                ];
            }
        };

        $resolver = new GraphqlResolver($readService, $this->registryWithPaths([]));
        $node = $resolver->category(new CategoryGraphqlNodeRequest('cat-1'));

        self::assertIsArray($node);
        self::assertSame('cat-1', $node['id']);
        self::assertSame('published', $node['status']);
        self::assertSame('root', $node['slug']);
    }

    public function testCategoryPathUsesProjectionPrefixes(): void
    {
        $readService = new class implements CategoryProjectionReadServiceInterface {
            public function list(?CategoryProjectionCriteria $criteria = null): array
            {
                return [];
            }

            public function tree(?CategoryProjectionCriteria $criteria = null): array
            {
                return [];
            }

            public function findOne(string $id): array
            {
                return [
                    'id' => $id,
                    'parent_id' => 'cat-root',
                    'slug' => 'electronics',
                    'name' => 'Electronics',
                    'locale' => 'en',
                    'tenant' => 'default',
                    'workflow_state' => 'published',
                    'published' => true,
                    'published_at' => '2025-11-02 00:00:00',
                    'updated_at' => '2025-11-02 00:00:00',
                    'path' => 'root.electronics',
                ];
            }
        };

        $rows = [
            ['id' => 'cat-root', 'parent_id' => null, 'slug' => 'root', 'name' => 'Root', 'locale' => 'en', 'workflow_state' => 'published', 'published' => true, 'path' => 'root'],
            ['id' => 'cat-2', 'parent_id' => 'cat-root', 'slug' => 'electronics', 'name' => 'Electronics', 'locale' => 'en', 'workflow_state' => 'published', 'published' => true, 'path' => 'root.electronics'],
        ];
        $resolver = new GraphqlResolver($readService, $this->registryWithPaths($rows));
        $path = $resolver->categoryPath(new CategoryGraphqlNodeRequest('cat-2'));

        self::assertCount(2, $path);
        self::assertSame('cat-root', $path[0]['id']);
        self::assertSame('cat-2', $path[1]['id']);
    }

    /** @param list<array<string,mixed>> $rows */
    private function registryWithPaths(array $rows): ManagerRegistry
    {
        /** @var Connection&MockObject $connection */
        $connection = $this->createMock(Connection::class);
        $connection->method('fetchAllAssociative')->willReturn($rows);

        /** @var ManagerRegistry&MockObject $registry */
        $registry = $this->createMock(ManagerRegistry::class);
        $registry->method('getConnection')->with('infra')->willReturn($connection);

        return $registry;
    }
}
