<?php

declare(strict_types=1);

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

namespace App\DataFixtures;

use App\Entity\CategoryAliasEntity;
use App\Entity\CategoryBanner;
use App\Entity\CategoryEntity;
use App\Entity\CategoryHtmlBlock;
use App\Entity\CategoryLink;
use App\Entity\CategoryPin;
use App\Entity\CategoryRedirect;
use App\Entity\CategoryTaxonomy;
use App\Entity\ProjectionControlEntity;
use App\Entity\VirtualCategoryEntity;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $root = new CategoryEntity('Root', 'root', 'root', 0);
        $electronics = new CategoryEntity('Electronics', 'electronics', 'root.electronics', 1);
        $phones = new CategoryEntity('Phones', 'phones', 'root.electronics.phones', 2);
        $gaming = new CategoryEntity('Gaming', 'gaming', 'root.electronics.gaming', 2);

        $manager->persist($root);
        $manager->persist($electronics);
        $manager->persist($phones);
        $manager->persist($gaming);

        $manager->persist(new CategoryAliasEntity('smartphones', $phones->getId()));
        $manager->persist(new CategoryBanner($electronics->getId(), 'Electronics event', 'Weekend campaign for electronics.'));
        $manager->persist(new CategoryHtmlBlock($phones->getId(), '<p>Phones buying guide</p>'));
        $manager->persist(new CategoryPin($gaming->getId(), 'record-featured-console', 10));

        $taxonomy = new CategoryTaxonomy(
            '01taxonomy00000000000000001',
            'catalog',
            ['en' => 'Catalog', 'uk' => 'Каталог'],
            ['scope' => 'default']
        );
        $manager->persist($taxonomy);

        $manager->persist(new CategoryLink(
            '01link000000000000000000001',
            $taxonomy->id(),
            $phones->getId(),
            'product',
            'ProductEntity',
            'prod-phone-001'
        ));

        $manager->persist(new CategoryRedirect(
            '01redirect0000000000000001',
            '/catalog/root/electronics/smartphones',
            '/catalog/root/electronics/phones'
        ));

        $manager->persist(new ProjectionControlEntity('category', false));

        $manager->persist(new VirtualCategoryEntity(
            '01virtual00000000000000001',
            'Featured phones',
            ['code' => 'featured-phone', 'expr' => 'tag == "featured"']
        ));

        $manager->flush();
    }
}
