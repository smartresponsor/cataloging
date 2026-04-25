<?php

# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\DataFixtures;

use App\Cataloging\Entity\CatalogCategoryEntity;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

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
        $root = new CatalogCategoryEntity('Catalog Broken Root', 'catalog-broken-root', 'catalog_broken_root', 0);
        $manager->persist($root);

        $slugA = 'broken';
        $slugB = 'node';
        $brokenPath = sprintf('%s.%s.%s', $root->getPath(), $slugA, $slugB);

        $broken = new CatalogCategoryEntity('Broken Child Node', 'broken-child-node', $brokenPath, 1);
        $manager->persist($broken);

        $manager->flush();
    }
}
