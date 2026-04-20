<?php

# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\DataFixtures;

use App\Cataloging\Entity\CatalogCategoryBanner;
use App\Cataloging\Entity\CatalogCategoryHtmlBlock;
use App\Cataloging\Entity\CategoryAliasEntity;
use App\Cataloging\Entity\CategoryEntity;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * Provides the category fixtures implementation.
 */
final class CategoryFixtures extends Fixture
{
    /**
     * Handles the load workflow.
     */
    public function load(ObjectManager $manager): void
    {
        $root = new CategoryEntity('Catalog', 'catalog', 'catalog', 0);
        $manager->persist($root);

        $branches = [$root];
        for ($index = 1; $index <= 24; ++$index) {
            $parent = $branches[array_rand($branches)];
            $slug = sprintf('demo-category-%d', $index);
            $name = sprintf('Demo category %d', $index);
            $path = $parent->getPath().'.'.$slug;
            $depth = $parent->getDepth() + 1;

            $category = new CategoryEntity($name, $slug, $path, $depth);
            $manager->persist($category);
            $branches[] = $category;

            if (0 === $index % 3) {
                $manager->persist(new CatalogCategoryBanner(
                    $category->getId(),
                    sprintf('Demo banner %d', $index),
                    'Demo banner content for catalog category.',
                ));
                $manager->persist(new CatalogCategoryHtmlBlock(
                    $category->getId(),
                    sprintf(
                        '<section class="p-3"><h2>%s</h2><p>%s</p></section>',
                        sprintf('Demo block %d', $index),
                        'Demo html block content for category.',
                    ),
                ));
            }

            if (0 === $index % 5) {
                $manager->persist(new CategoryAliasEntity(
                    sprintf('demo-alias-%d', $index),
                    $category->getId(),
                ));
            }
        }

        $manager->flush();
    }
}
