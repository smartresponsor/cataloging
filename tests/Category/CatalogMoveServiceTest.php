<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Tests\Category;

use App\Service\CatalogMoveService;
use PHPUnit\Framework\TestCase;

final class CatalogMoveServiceTest extends TestCase
{
    public function testMoveRebasesSubtreeAndReturnsRedirects(): void
    {
        $pdo = $this->createPdo();
        $service = new CatalogMoveService($pdo);

        /** @var array{0:int,1:list<array{id:string,from:string,to:string}>} $result */
        $result = $service->move('electronics', 'fashion', 'main-tree', 'strict');
        [$changed, $redirects] = $result;

        self::assertSame(2, $changed);
        self::assertCount(2, $redirects);
        self::assertSame('root.electronics', $redirects[0]['from']);
        self::assertSame('root.fashion.electronics', $redirects[0]['to']);
        self::assertSame('root.electronics.phones', $redirects[1]['from']);
        self::assertSame('root.fashion.electronics.phones', $redirects[1]['to']);

        $statement = $pdo->query('SELECT id, path, depth FROM category ORDER BY id ASC');
        self::assertInstanceOf(\PDOStatement::class, $statement);
        $rows = $statement->fetchAll(\PDO::FETCH_ASSOC);
        $indexed = [];
        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }
            $fromId = $row['id'] ?? null;
            $fromPath = $row['path'] ?? null;
            $fromDepth = $row['depth'] ?? null;
            if (!is_scalar($fromId) || !is_scalar($fromPath) || !is_scalar($fromDepth)) {
                continue;
            }
            $indexed[(string) $fromId] = ['id' => (string) $fromId, 'path' => (string) $fromPath, 'depth' => (string) $fromDepth];
        }

        self::assertSame('root.fashion.electronics', $indexed['electronics']['path']);
        self::assertSame(2, (int) $indexed['electronics']['depth']);
        self::assertSame('root.fashion.electronics.phones', $indexed['phones']['path']);
        self::assertSame(3, (int) $indexed['phones']['depth']);
    }

    public function testMoveDryRunRollsBackChanges(): void
    {
        $pdo = $this->createPdo();
        $service = new CatalogMoveService($pdo);

        /** @var array{0:int,1:list<array{id:string,from:string,to:string}>} $result */
        $result = $service->move('electronics', 'fashion', 'main-tree', 'strict', true, 'en_US');
        [$changed, $redirects] = $result;

        self::assertSame(2, $changed);
        self::assertCount(2, $redirects);

        $statement = $pdo->query("SELECT path FROM category WHERE id = 'electronics'");
        self::assertInstanceOf(\PDOStatement::class, $statement);
        $path = $statement->fetchColumn();
        self::assertSame('root.electronics', $path);
    }

    public function testMoveRejectsCycles(): void
    {
        $pdo = $this->createPdo();
        $service = new CatalogMoveService($pdo);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot move a node under its own descendant.');

        $service->move('electronics', 'phones', 'main-tree', 'strict');
    }

    private function createPdo(): \PDO
    {
        $pdo = new \PDO('sqlite::memory:');
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $pdo->exec('CREATE TABLE category (id TEXT PRIMARY KEY, slug TEXT NOT NULL, path TEXT NOT NULL, depth INTEGER NOT NULL)');
        $pdo->exec("INSERT INTO category (id, slug, path, depth) VALUES
            ('root', 'root', 'root', 0),
            ('electronics', 'electronics', 'root.electronics', 1),
            ('phones', 'phones', 'root.electronics.phones', 2),
            ('fashion', 'fashion', 'root.fashion', 1)
        ");

        return $pdo;
    }
}
