<?php

# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\CategoryAliasEntity;
use App\Entity\CategoryBanner;
use App\Entity\CategoryEntity;
use App\Entity\CategoryHtmlBlock;
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
        $fakerFactoryClass = 'Faker\\Factory';
        $faker = class_exists($fakerFactoryClass) ? $fakerFactoryClass::create('en_US') : null;
        $faker?->seed(2025);

        $root = new CategoryEntity('Catalog', 'catalog', 'catalog', 0);
        $manager->persist($root);

        $branches = [$root];
        for ($index = 1; $index <= 24; ++$index) {
            $parent = $branches[array_rand($branches)];
            $slug = null !== $faker
                ? sprintf('%s-%d', $faker->unique()->slug(2), $index)
                : sprintf('demo-category-%d', $index);
            $name = $faker?->words(2, true) ?? sprintf('Demo category %d', $index);
            $path = $parent->getPath().'.'.$slug;
            $depth = $parent->getDepth() + 1;

            $category = new CategoryEntity($name, $slug, $path, $depth);
            $manager->persist($category);
            $branches[] = $category;

            if (0 === $index % 3) {
                $manager->persist(new CategoryBanner(
                    $category->getId(),
                    $faker?->sentence(4) ?? sprintf('Demo banner %d', $index),
                    $faker?->paragraph(2) ?? 'Demo banner content for catalog category.',
                ));
                $manager->persist(new CategoryHtmlBlock(
                    $category->getId(),
                    sprintf(
                        '<section class="p-3"><h2>%s</h2><p>%s</p></section>',
                        $faker?->sentence(3) ?? sprintf('Demo block %d', $index),
                        $faker?->sentence(8) ?? 'Demo html block content for category.',
                    ),
                ));
            }

            if (0 === $index % 5) {
                $manager->persist(new CategoryAliasEntity(
                    $faker?->slug(2) ?? sprintf('demo-alias-%d', $index),
                    $category->getId(),
                ));
            }
        }

        $manager->flush();
    }
}
