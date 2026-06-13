<?php

# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\DataFixtures;

use App\Cataloging\Entity\Catalog\CatalogCategoryBannerEntity;
use App\Cataloging\Entity\Catalog\CatalogCategoryEntity;
use App\Cataloging\Entity\Catalog\CatalogCategoryHtmlBlockEntity;
use App\Cataloging\Entity\Catalog\CatalogCategorySlugHistoryEntity;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Uid\Uuid;

/**
 * Provides the category fixtures implementation.
 */
final class CategoryFixtures extends Fixture
{
    private const ICONS = [
        '/fixtures/images/category-icon.svg',
        '/fixtures/images/avatar-user.svg',
        '/fixtures/images/product-card.svg',
        '/fixtures/images/vendor-logo.svg',
    ];

    private const CATEGORY_NAMES = [
        'Electronics',
        'Phones',
        'Laptops',
        'Audio',
        'Smart Home',
        'Gaming',
        'Home & Kitchen',
        'Appliances',
        'Beauty',
        'Health & Wellness',
        'Fashion',
        'Shoes',
        'Sports & Outdoors',
        'Fitness',
        'Baby & Kids',
        'Toys',
        'Office Supplies',
        'Furniture',
        'Garden',
        'Pet Supplies',
        'Auto Accessories',
        'Gift Cards',
        'Outlet',
        'Best Sellers',
    ];

    /**
     * Handles the load workflow.
     */
    public function load(ObjectManager $manager): void
    {
        $rootSlug = Uuid::v7()->toRfc4122();
        $root = new CatalogCategoryEntity('Marketplace', $rootSlug, $rootSlug, 0);
        $root->setIconUrl(self::ICONS[0]);
        $manager->persist($root);

        $branches = [$root];
        for ($index = 1; $index <= 24; ++$index) {
            $parent = $branches[array_rand($branches)];
            $slug = Uuid::v7()->toRfc4122();
            $nameEntity = self::CATEGORY_NAMES[$index - 1] ?? sprintf('Catalog section %d', $index);
            $path = $parent->getPath().'.'.$slug;
            $depth = $parent->getDepth() + 1;

            $category = new CatalogCategoryEntity($nameEntity, $slug, $path, $depth);
            $category->setIconUrl(self::ICONS[$index % count(self::ICONS)]);
            $manager->persist($category);
            $manager->flush();
            $branches[] = $category;

            if (0 === $index % 3) {
                $manager->persist(new CatalogCategoryBannerEntity(
                    (string) $category->getId(),
                    sprintf('%s showcase banner', $nameEntity),
                    sprintf('%s featured collection for seasonal merchandising and upsell placements.', $nameEntity),
                ));
                $manager->persist(new CatalogCategoryHtmlBlockEntity(
                    (string) $category->getId(),
                    sprintf(
                        '<section class="p-3"><h2>%s</h2><p>%s</p></section>',
                        sprintf('%s buying guide', $nameEntity),
                        sprintf('Shop curated %s with bundles, gifts, and seasonal promotions.', strtolower($nameEntity)),
                    ),
                ));
            }

            if (0 === $index % 5) {
                $manager->persist(new CatalogCategorySlugHistoryEntity(
                    sprintf('storefront-slug-history-%d', $index),
                    (string) $category->getId(),
                ));
            }
        }

        $manager->flush();
    }
}
