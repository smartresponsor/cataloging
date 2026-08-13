<?php

# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\DataFixtures;

use App\Cataloging\Entity\Catalog\CatalogCatalogEntity;
use App\Cataloging\Entity\Catalog\CatalogCategoryEntity;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Uid\Uuid;

/**
 * Provides the broken tree fixtures implementation.
 */
final class BrokenTreeFixtures extends Fixture
{
    /**
     * Handles the load workflow.
     */
    public function load(ObjectManager $manager): void
    {
        $catalog = new CatalogCatalogEntity('broken-tree', 'Broken Tree', 'integrity-testing');
        $manager->persist($catalog);

        $rootSlug = Uuid::v7()->toRfc4122();
        $root = new CatalogCategoryEntity($catalog, 'Catalog Broken Root', $rootSlug, $rootSlug, 0);
        $manager->persist($root);

        $slugA = Uuid::v7()->toRfc4122();
        $slugB = Uuid::v7()->toRfc4122();
        $brokenPath = sprintf('%s.%s.%s', $root->getPath(), $slugA, $slugB);

        $broken = new CatalogCategoryEntity($catalog, 'Broken Child Node', Uuid::v7()->toRfc4122(), $brokenPath, 1);
        $manager->persist($broken);

        $manager->flush();
    }
}
