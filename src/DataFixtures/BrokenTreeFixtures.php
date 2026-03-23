<?php
# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class BrokenTreeFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // would create a broken tree to test rebuild command
    }
}
