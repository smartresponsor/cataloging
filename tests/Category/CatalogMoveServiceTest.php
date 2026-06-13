<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Tests\CategoryEntity;

use App\Cataloging\Service\CatalogMoveService;
use App\Cataloging\Tests\Support\CategoryDoctrineEntityManagerFactory;
use App\Cataloging\ValueObject\CatalogMoveRequest;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

final class CatalogMoveServiceTest extends TestCase
{
    private const ROOT_ID = '1';
    private const ELECTRONICS_ID = '2';
    private const PHONES_ID = '3';
    private const FASHION_ID = '4';
    private const ROOT_SLUG = '018f4f0e-5d8c-7a3c-a0d4-1bf3d8c6a501';
    private const ELECTRONICS_SLUG = '018f4f0e-5d8c-7a3c-a0d4-1bf3d8c6a502';
    private const PHONES_SLUG = '018f4f0e-5d8c-7a3c-a0d4-1bf3d8c6a503';
    private const FASHION_SLUG = '018f4f0e-5d8c-7a3c-a0d4-1bf3d8c6a504';

    public function testMoveRebasesSubtreeAndReturnsRedirects(): void
    {
        $connection = $this->createConnection();
        $entityManager = $this->createEntityManager($connection);
        $service = new CatalogMoveService($entityManager);

        /** @var array{0:int,1:list<array{id:string,from:string,to:string}>} $result */
        $result = $service->move(new CatalogMoveRequest(self::ELECTRONICS_ID, self::FASHION_ID, 'main-tree', 'strict'));
        [$changed, $redirects] = $result;

        self::assertSame(2, $changed);
        self::assertCount(2, $redirects);
        self::assertSame(self::ROOT_SLUG.'.'.self::ELECTRONICS_SLUG, $redirects[0]['from']);
        self::assertSame(self::ROOT_SLUG.'.'.self::FASHION_SLUG.'.'.self::ELECTRONICS_SLUG, $redirects[0]['to']);
        self::assertSame(self::ROOT_SLUG.'.'.self::ELECTRONICS_SLUG.'.'.self::PHONES_SLUG, $redirects[1]['from']);
        self::assertSame(self::ROOT_SLUG.'.'.self::FASHION_SLUG.'.'.self::ELECTRONICS_SLUG.'.'.self::PHONES_SLUG, $redirects[1]['to']);

        $rows = $connection->fetchAllAssociative('SELECT id, path, depth FROM category ORDER BY id ASC');
        $indexed = [];
        foreach ($rows as $row) {
            $fromId = $row['id'] ?? null;
            $fromPath = $row['path'] ?? null;
            $fromDepth = $row['depth'] ?? null;
            if (!is_scalar($fromId) || !is_scalar($fromPath) || !is_scalar($fromDepth)) {
                continue;
            }
            $indexed[(string) $fromId] = ['id' => (string) $fromId, 'path' => (string) $fromPath, 'depth' => (string) $fromDepth];
        }

        self::assertSame(self::ROOT_SLUG.'.'.self::FASHION_SLUG.'.'.self::ELECTRONICS_SLUG, $indexed[self::ELECTRONICS_ID]['path']);
        self::assertSame(2, (int) $indexed[self::ELECTRONICS_ID]['depth']);
        self::assertSame(self::ROOT_SLUG.'.'.self::FASHION_SLUG.'.'.self::ELECTRONICS_SLUG.'.'.self::PHONES_SLUG, $indexed[self::PHONES_ID]['path']);
        self::assertSame(3, (int) $indexed[self::PHONES_ID]['depth']);
    }

    public function testMoveDryRunRollsBackChanges(): void
    {
        $connection = $this->createConnection();
        $entityManager = $this->createEntityManager($connection);
        $service = new CatalogMoveService($entityManager);

        /** @var array{0:int,1:list<array{id:string,from:string,to:string}>} $result */
        $result = $service->move(new CatalogMoveRequest(self::ELECTRONICS_ID, self::FASHION_ID, 'main-tree', 'strict', true, 'en_US'));
        [$changed, $redirects] = $result;

        self::assertSame(2, $changed);
        self::assertCount(2, $redirects);

        $path = $connection->fetchOne('SELECT path FROM category WHERE id = 2');
        self::assertIsScalar($path);
        self::assertSame(self::ROOT_SLUG.'.'.self::ELECTRONICS_SLUG, (string) $path);
    }

    public function testMoveRejectsCycles(): void
    {
        $connection = $this->createConnection();
        $entityManager = $this->createEntityManager($connection);
        $service = new CatalogMoveService($entityManager);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot move a node under its own descendant.');

        $service->move(new CatalogMoveRequest(self::ELECTRONICS_ID, self::PHONES_ID, 'main-tree', 'strict'));
    }

    private function createConnection(): Connection
    {
        $connection = CategoryDoctrineEntityManagerFactory::createConnection();
        $connection->executeStatement('CREATE TABLE category (id INTEGER PRIMARY KEY, nameEntity TEXT NOT NULL DEFAULT "", slug TEXT NOT NULL, parent_id INTEGER DEFAULT NULL, path TEXT NOT NULL, depth INTEGER NOT NULL, locale TEXT DEFAULT NULL, tenant TEXT NOT NULL DEFAULT "default", workflow_state TEXT NOT NULL DEFAULT "draft", published INTEGER NOT NULL DEFAULT 0, published_at TEXT DEFAULT NULL, icon_url TEXT DEFAULT NULL)');
        $connection->executeStatement("INSERT INTO category (id, nameEntity, slug, parent_id, path, depth, locale, tenant, workflow_state, published, published_at, icon_url) VALUES
            (1, 'Root', '".self::ROOT_SLUG."', NULL, '".self::ROOT_SLUG."', 0, NULL, 'default', 'draft', 0, NULL, NULL),
            (2, 'Electronics', '".self::ELECTRONICS_SLUG."', 1, '".self::ROOT_SLUG.'.'.self::ELECTRONICS_SLUG."', 1, NULL, 'default', 'draft', 0, NULL, NULL),
            (3, 'Phones', '".self::PHONES_SLUG."', 2, '".self::ROOT_SLUG.'.'.self::ELECTRONICS_SLUG.'.'.self::PHONES_SLUG."', 2, NULL, 'default', 'draft', 0, NULL, NULL),
            (4, 'Fashion', '".self::FASHION_SLUG."', 1, '".self::ROOT_SLUG.'.'.self::FASHION_SLUG."', 1, NULL, 'default', 'draft', 0, NULL, NULL)
        ");

        return $connection;
    }

    private function createEntityManager(Connection $connection): EntityManagerInterface
    {
        return CategoryDoctrineEntityManagerFactory::createEntityManager($connection);
    }
}
