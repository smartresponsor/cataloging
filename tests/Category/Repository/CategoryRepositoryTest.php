<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Tests\Category\Repository;

use App\Repository\CategoryRepository;
use PHPUnit\Framework\TestCase;

final class CategoryRepositoryTest extends TestCase
{
    public function testCreateReturnsCanonicalShape(): void
    {
        $repository = new CategoryRepository();

        $created = $repository->create('tax-1', 'root-1', ['en' => 'Phones'], ['en' => 'phones'], ['published' => false]);

        self::assertSame('tax-1', $created['taxonomyId']);
        self::assertSame('root-1', $created['parentId']);
        self::assertSame(['en' => 'Phones'], $created['name']);
        self::assertSame(['en' => 'phones'], $created['slug']);
        self::assertSame(['published' => false], $created['meta']);
        self::assertArrayHasKey('path', $created);
        self::assertArrayHasKey('order', $created);
    }

    public function testMoveReturnsUpdatedParentAndOrder(): void
    {
        $repository = new CategoryRepository();
        $root = $repository->create('tax-1', null, ['en' => 'Root'], ['en' => 'root'], []);
        $child = $repository->create('tax-1', null, ['en' => 'Phones'], ['en' => 'phones'], []);

        $moved = $repository->move('actor-1', (string) $child['id'], (string) $root['id'], 7);

        self::assertSame((string) $child['id'], $moved['id']);
        self::assertSame((string) $root['id'], $moved['parentId']);
        self::assertSame(7, $moved['order']);
        self::assertSame('/root/phones', $repository->fullSlug((string) $child['id'], 'en'));
    }

    public function testBySlugAndBreadcrumbReflectPersistedHierarchy(): void
    {
        $repository = new CategoryRepository();
        $root = $repository->create('tax-1', null, ['en' => 'Root'], ['en' => 'root'], []);
        $child = $repository->create('tax-1', (string) $root['id'], ['en' => 'Laptops'], ['en' => 'laptops'], []);

        $resolved = $repository->bySlug('tax-1', 'laptops', 'en');
        $breadcrumb = $repository->breadcrumb((string) $child['id'], 'en');
        $tree = $repository->tree('tax-1', null, 3, 'en');

        self::assertSame((string) $child['id'], $resolved['id']);
        self::assertSame('/root/laptops', $resolved['path']);
        self::assertCount(2, $breadcrumb);
        self::assertSame('Root', $breadcrumb[0]['name']);
        self::assertSame('Laptops', $breadcrumb[1]['name']);
        self::assertCount(2, $tree);
        self::assertSame('root', $tree[0]['slug']);
        self::assertSame('laptops', $tree[1]['slug']);
    }
}
