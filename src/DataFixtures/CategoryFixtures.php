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
 * Provides deterministic, business-oriented marketplace category fixtures.
 */
final class CategoryFixtures extends Fixture
{
    private const MARKETPLACE_IMAGE = 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?auto=format&fit=crop&w=1200&q=80';

    /**
     * @var list<array{
     *     name: string,
     *     image: string,
     *     description: string,
     *     children: list<array{name: string, image: string, description: string}>
     * }>
     */
    private const CATALOGS = [
        [
            'name' => 'Task Catalog',
            'image' => 'https://images.unsplash.com/photo-1504917595217-d4dc5ebe6122?auto=format&fit=crop&w=1200&q=80',
            'description' => 'Customer-requested work organized by the job that needs to be completed.',
            'children' => [
                ['name' => 'Appliance Installation', 'image' => 'https://images.unsplash.com/photo-1556911220-bff31c812dba?auto=format&fit=crop&w=1200&q=80', 'description' => 'Install dishwashers, ranges, microwaves, washers, dryers, and other household appliances.'],
                ['name' => 'Furniture Assembly', 'image' => 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?auto=format&fit=crop&w=1200&q=80', 'description' => 'Assemble beds, desks, shelving, cabinets, patio furniture, and storage systems.'],
                ['name' => 'Home Repair', 'image' => 'https://images.unsplash.com/photo-1581578731548-c64695cc6952?auto=format&fit=crop&w=1200&q=80', 'description' => 'Repair drywall, doors, trim, fixtures, hardware, and common household damage.'],
                ['name' => 'Mounting and Hanging', 'image' => 'https://images.unsplash.com/photo-1524758631624-e2822e304c36?auto=format&fit=crop&w=1200&q=80', 'description' => 'Mount televisions, mirrors, artwork, shelves, curtain rods, and wall accessories.'],
                ['name' => 'Outdoor Projects', 'image' => 'https://images.unsplash.com/photo-1416879595882-3373a0480b5b?auto=format&fit=crop&w=1200&q=80', 'description' => 'Complete fence, gate, patio, yard, exterior fixture, and seasonal outdoor tasks.'],
            ],
        ],
        [
            'name' => 'Order Catalog',
            'image' => 'https://images.unsplash.com/photo-1450101499163-c8848c66ca85?auto=format&fit=crop&w=1200&q=80',
            'description' => 'Ready-to-request job packages with a clear scope, expected duration, and purchasing path.',
            'children' => [
                ['name' => 'Same-Day Help', 'image' => 'https://images.unsplash.com/photo-1504384308090-c894fdcc538d?auto=format&fit=crop&w=1200&q=80', 'description' => 'Urgent household jobs that can be accepted and completed on the same day.'],
                ['name' => 'Scheduled Installation', 'image' => 'https://images.unsplash.com/photo-1503387762-592deb58ef4e?auto=format&fit=crop&w=1200&q=80', 'description' => 'Book installation work for a selected date and time window.'],
                ['name' => 'Multi-Step Project', 'image' => 'https://images.unsplash.com/photo-1504307651254-35680f356dfd?auto=format&fit=crop&w=1200&q=80', 'description' => 'Coordinate jobs that require assessment, materials, several visits, or multiple milestones.'],
                ['name' => 'Property Turnover', 'image' => 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=1200&q=80', 'description' => 'Prepare apartments and homes between occupants with repair, assembly, mounting, and cleanup tasks.'],
                ['name' => 'Business Maintenance', 'image' => 'https://images.unsplash.com/photo-1497366754035-f200968a6e72?auto=format&fit=crop&w=1200&q=80', 'description' => 'Repeatable maintenance and repair orders for offices, retail spaces, and managed properties.'],
            ],
        ],
        [
            'name' => 'Product Catalog',
            'image' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?auto=format&fit=crop&w=1200&q=80',
            'description' => 'Physical products and project materials available for direct purchase or addition to a service order.',
            'children' => [
                ['name' => 'Tools and Equipment', 'image' => 'https://images.unsplash.com/photo-1530124566582-a618bc2615dc?auto=format&fit=crop&w=1200&q=80', 'description' => 'Hand tools, power tools, measuring equipment, ladders, and jobsite accessories.'],
                ['name' => 'Hardware and Fasteners', 'image' => 'https://images.unsplash.com/photo-1504148455328-c376907d081c?auto=format&fit=crop&w=1200&q=80', 'description' => 'Anchors, screws, brackets, hinges, handles, fittings, and installation hardware.'],
                ['name' => 'Fixtures and Parts', 'image' => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?auto=format&fit=crop&w=1200&q=80', 'description' => 'Replacement fixtures, appliance parts, plumbing parts, electrical accessories, and repair components.'],
                ['name' => 'Home and Storage', 'image' => 'https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?auto=format&fit=crop&w=1200&q=80', 'description' => 'Shelving, organizers, furniture, storage systems, and practical home products.'],
                ['name' => 'Safety and Protection', 'image' => 'https://images.unsplash.com/photo-1513828583688-c52646db42da?auto=format&fit=crop&w=1200&q=80', 'description' => 'Protective equipment, surface protection, detectors, locks, and jobsite safety supplies.'],
            ],
        ],
        [
            'name' => 'Service Catalog',
            'image' => 'https://images.unsplash.com/photo-1581094794329-c8112a89af12?auto=format&fit=crop&w=1200&q=80',
            'description' => 'Provider-authored service offers describing what a handyman performs for a customer.',
            'children' => [
                ['name' => 'Installation Services', 'image' => 'https://images.unsplash.com/photo-1505798577917-a65157d3320a?auto=format&fit=crop&w=1200&q=80', 'description' => 'Published installation offers with scope, inclusions, exclusions, duration, and service area.'],
                ['name' => 'Repair Services', 'image' => 'https://images.unsplash.com/photo-1621905252507-b35492cc74b4?auto=format&fit=crop&w=1200&q=80', 'description' => 'Diagnostic and repair offers for common home, fixture, furniture, and appliance problems.'],
                ['name' => 'Assembly Services', 'image' => 'https://images.unsplash.com/photo-1598300053650-a7c4b7c9a2ac?auto=format&fit=crop&w=1200&q=80', 'description' => 'Fixed-scope assembly offers for furniture, storage, fitness, office, and outdoor products.'],
                ['name' => 'Maintenance Services', 'image' => 'https://images.unsplash.com/photo-1581579185169-7f6fe75f6525?auto=format&fit=crop&w=1200&q=80', 'description' => 'Recurring property care, inspection, adjustment, replacement, and preventive maintenance services.'],
                ['name' => 'Project Services', 'image' => 'https://images.unsplash.com/photo-1541888946425-d81bb19240f5?auto=format&fit=crop&w=1200&q=80', 'description' => 'Larger scoped offers combining labor, milestones, materials, scheduling, and customer approvals.'],
            ],
        ],
    ];

    public function load(ObjectManager $manager): void
    {
        $root = $this->createCategory($manager, 'Marketplace', self::MARKETPLACE_IMAGE, null);

        foreach (self::CATALOGS as $catalogIndex => $catalogDefinition) {
            $catalog = $this->createCategory(
                $manager,
                $catalogDefinition['name'],
                $catalogDefinition['image'],
                $root,
            );

            $this->addPresentationContent(
                $manager,
                $catalog,
                $catalogDefinition['name'],
                $catalogDefinition['description'],
                true,
            );

            foreach ($catalogDefinition['children'] as $childIndex => $childDefinition) {
                $category = $this->createCategory(
                    $manager,
                    $childDefinition['name'],
                    $childDefinition['image'],
                    $catalog,
                );

                $this->addPresentationContent(
                    $manager,
                    $category,
                    $childDefinition['name'],
                    $childDefinition['description'],
                    0 === $childIndex % 2,
                );

                if (0 === ($catalogIndex + $childIndex) % 3) {
                    $manager->persist(new CatalogCategorySlugHistoryEntity(
                        sprintf('%s-%s', $this->slugify($catalogDefinition['name']), $this->slugify($childDefinition['name'])),
                        (string) $category->getId(),
                    ));
                }
            }
        }

        $manager->flush();
    }

    private function createCategory(
        ObjectManager $manager,
        string $name,
        string $image,
        ?CatalogCategoryEntity $parent,
    ): CatalogCategoryEntity {
        $slug = Uuid::v7()->toRfc4122();
        $path = null === $parent ? $slug : $parent->getPath().'.'.$slug;
        $depth = null === $parent ? 0 : $parent->getDepth() + 1;

        $category = new CatalogCategoryEntity($name, $slug, $path, $depth);
        $category->setIconUrl($image);
        $manager->persist($category);
        $manager->flush();

        return $category;
    }

    private function addPresentationContent(
        ObjectManager $manager,
        CatalogCategoryEntity $category,
        string $title,
        string $description,
        bool $featured,
    ): void {
        if ($featured) {
            $manager->persist(new CatalogCategoryBannerEntity(
                (string) $category->getId(),
                $title,
                $description,
            ));
        }

        $manager->persist(new CatalogCategoryHtmlBlockEntity(
            (string) $category->getId(),
            sprintf(
                '<section class="catalog-category-introduction"><h2>%s</h2><p>%s</p></section>',
                htmlspecialchars($title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
                htmlspecialchars($description, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'),
            ),
        ));
    }

    private function slugify(string $value): string
    {
        return strtolower(str_replace([' ', '&'], ['-', 'and'], $value));
    }
}
