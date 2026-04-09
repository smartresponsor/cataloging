<?php

# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\CategoryEntity;
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
        $fakerFactoryClass = 'Faker\\Factory';
        $faker = class_exists($fakerFactoryClass) ? $fakerFactoryClass::create('en_US') : null;
        if (null !== $faker) {
            $faker->seed(404);
        }

        $root = new CategoryEntity('Catalog Broken Root', 'catalog-broken-root', 'catalog_broken_root', 0);
        $manager->persist($root);

        $slugA = null !== $faker ? $faker->slug(1) : 'broken';
        $slugB = null !== $faker ? $faker->slug(1) : 'node';
        $brokenPath = sprintf('%s.%s.%s', $root->getPath(), $slugA, $slugB);

        $broken = new CategoryEntity('Broken Child Node', 'broken-child-node', $brokenPath, 1);
        $manager->persist($broken);

        $manager->flush();
    }
}
