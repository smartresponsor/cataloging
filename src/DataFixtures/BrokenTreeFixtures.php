<?php

declare(strict_types=1);

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

namespace App\DataFixtures;

use App\Entity\CategoryEntity;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class BrokenTreeFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $root = new CategoryEntity('Broken Root', 'broken-root', 'broken-root', 0);
        $child = new CategoryEntity('Broken Child', 'broken-child', 'broken-root.orphan-child', 5);

        $manager->persist($root);
        $manager->persist($child);
        $manager->flush();
    }
}
