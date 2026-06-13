<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Tests\CategoryEntity;

use App\Cataloging\Entity\Catalog\CatalogCategoryEntity;
use PHPUnit\Framework\TestCase;

final class CatalogCategoryEntityTreePathTest extends TestCase
{
    public function testGetParentPathReturnsNullForRootNode(): void
    {
        $root = $this->createCatalogCategoryEntity('catalog', 0);

        self::assertNull($root->getParentPath());
    }

    public function testGetParentPathReturnsAncestorPath(): void
    {
        $child = $this->createCatalogCategoryEntity('catalog.electronics.phones', 2);

        self::assertSame('catalog.electronics', $child->getParentPath());
    }

    public function testIsDirectChildOfChecksDepthAndPath(): void
    {
        $parent = $this->createCatalogCategoryEntity('catalog.electronics', 1);
        $child = $this->createCatalogCategoryEntity('catalog.electronics.phones', 2);
        $grandChild = $this->createCatalogCategoryEntity('catalog.electronics.phones.android', 3);
        $sameDepth = $this->createCatalogCategoryEntity('catalog.electronics.laptops', 2);

        self::assertTrue($child->isDirectChildOf($parent));
        self::assertFalse($grandChild->isDirectChildOf($parent));
        self::assertFalse($sameDepth->isDirectChildOf($child));
    }

    private function createCatalogCategoryEntity(string $path, int $depth): CatalogCategoryEntity
    {
        $reflection = new \ReflectionClass(CatalogCategoryEntity::class);
        $entity = $reflection->newInstanceWithoutConstructor();

        $pathProperty = $reflection->getProperty('path');
        $pathProperty->setValue($entity, $path);

        $depthProperty = $reflection->getProperty('depth');
        $depthProperty->setValue($entity, $depth);

        return $entity;
    }
}
