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
use Faker\Factory;

final class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = class_exists(Factory::class) ? Factory::create('en_US') : null;
        if (null !== $faker) {
            $faker->seed(2025);
        }

        $root = new CategoryEntity('Catalog', 'catalog', 'catalog', 0);
        $manager->persist($root);

        $branches = [$root];
        for ($index = 1; $index <= 24; ++$index) {
            $parent = $branches[array_rand($branches)];
            $slug = null !== $faker
                ? sprintf('%s-%d', $faker->unique()->slug(2), $index)
                : sprintf('demo-category-%d', $index);
            $name = null !== $faker ? $faker->words(2, true) : sprintf('Demo category %d', $index);
            $path = $parent->getPath().'.'.$slug;
            $depth = $parent->getDepth() + 1;

            $category = new CategoryEntity($name, $slug, $path, $depth);
            $manager->persist($category);
            $branches[] = $category;

            if (0 === $index % 3) {
                $manager->persist(new CategoryBanner(
                    $category->getId(),
                    null !== $faker ? $faker->sentence(4) : sprintf('Demo banner %d', $index),
                    null !== $faker ? $faker->paragraph(2) : 'Demo banner content for catalog category.',
                ));
                $manager->persist(new CategoryHtmlBlock(
                    $category->getId(),
                    sprintf(
                        '<section class="p-3"><h2>%s</h2><p>%s</p></section>',
                        null !== $faker ? $faker->sentence(3) : sprintf('Demo block %d', $index),
                        null !== $faker ? $faker->sentence(8) : 'Demo html block content for category.',
                    ),
                ));
            }

            if (0 === $index % 5) {
                $manager->persist(new CategoryAliasEntity(
                    null !== $faker ? $faker->slug(2) : sprintf('demo-alias-%d', $index),
                    $category->getId(),
                ));
            }
        }

        $manager->flush();
    }
}
