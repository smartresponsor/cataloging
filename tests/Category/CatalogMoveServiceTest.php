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

        [$changed, $redirects] = $service->move('electronics', 'fashion', 'main-tree', 'strict');

        self::assertSame(2, $changed);
        self::assertCount(2, $redirects);
        self::assertSame('root.electronics', $redirects[0]['from']);
        self::assertSame('root.fashion.electronics', $redirects[0]['to']);
        self::assertSame('root.electronics.phones', $redirects[1]['from']);
        self::assertSame('root.fashion.electronics.phones', $redirects[1]['to']);

        $rows = $pdo->query('SELECT id, path, depth FROM category ORDER BY id ASC')->fetchAll(\PDO::FETCH_ASSOC);
        $indexed = [];
        foreach ($rows as $row) {
            $indexed[(string) $row['id']] = $row;
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

        [$changed, $redirects] = $service->move('electronics', 'fashion', 'main-tree', 'strict', true, 'en_US');

        self::assertSame(2, $changed);
        self::assertCount(2, $redirects);

        $path = $pdo->query("SELECT path FROM category WHERE id = 'electronics'")->fetchColumn();
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
