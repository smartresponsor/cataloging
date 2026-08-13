<?php

declare(strict_types=1);

namespace App\Cataloging\DataFixtures;

use App\Cataloging\Entity\Catalog\CatalogCatalogEntity;
use App\Cataloging\Entity\Catalog\CatalogCategoryEntity;
use App\Cataloging\Entity\Catalog\CatalogCategoryProjectionEntity;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;

final class MultiCatalogFixtures extends Fixture implements FixtureGroupInterface
{
    private const TREES = [
        'services' => ['Services', 'service-discovery', [
            'Home Cleaning' => ['Standard Home Cleaning', 'Apartment Cleaning', 'Deep Cleaning', 'Move-In Cleaning', 'Move-Out Cleaning', 'Office Cleaning', 'Carpet Cleaning', 'Interior Window Cleaning'],
            'Furniture Assembly' => ['Standard Furniture Assembly', 'IKEA Furniture Assembly', 'Fitness Equipment Assembly', 'Office Furniture Assembly', 'Shelving Assembly', 'Bed Assembly', 'Furniture Disassembly'],
            'TV Mounting' => ['Standard TV Mounting', 'Over-Fireplace TV Mounting', 'Large TV Mounting', 'Corner TV Mounting', 'Soundbar Mounting', 'Cable Concealment'],
            'Picture Hanging' => ['Single Picture Hanging', 'Gallery Wall Installation', 'Art Installation', 'Whiteboard Installation'],
            'Mirror Hanging' => ['Small Mirror Hanging', 'Large Mirror Hanging', 'Heavy Mirror Hanging'],
            'Shelf Mounting' => ['Single Shelf Mounting', 'Floating Shelf Installation', 'Storage Rack Installation'],
            'Window Treatment Installation' => ['Curtain Rod Installation', 'Window Blind Installation', 'Shade Installation'],
            'Lighting Installation' => ['Light Fixture Installation', 'Chandelier Installation', 'Pendant Light Installation', 'Light Fixture Replacement'],
            'Ceiling Fan Installation' => ['New Ceiling Fan Installation', 'Ceiling Fan Replacement', 'Ceiling Fan Troubleshooting'],
            'Smart Home Installation' => ['Smart Thermostat Installation', 'Smart Home Setup', 'Smart Lighting Setup', 'Wi-Fi Device Setup'],
            'Security Device Installation' => ['Security Camera Installation', 'Video Doorbell Installation', 'Smart Lock Installation', 'Home Security System Setup'],
            'Home Theater Setup' => ['TV Setup', 'Soundbar Installation', 'Speaker Installation', 'Streaming Device Setup', 'Gaming Console Setup', 'Cable Management'],
            'Computer Support' => ['Computer Setup', 'Computer Repair', 'Printer Setup', 'Data Transfer', 'Technical Support'],
            'Wi-Fi Setup' => ['Router Installation', 'Wi-Fi Setup', 'Network Troubleshooting', 'Mesh Wi-Fi Installation'],
            'Appliance Installation' => ['Washer Installation', 'Dryer Installation', 'Dishwasher Installation', 'Refrigerator Setup', 'Microwave Installation'],
            'Appliance Repair' => ['Major Appliance Repair', 'Small Appliance Repair', 'Appliance Troubleshooting'],
            'Packing Services' => ['Home Packing', 'Office Packing', 'Move-In Unpacking', 'Move-Out Packing'],
            'In-Home Moving' => ['Furniture Moving', 'Heavy Lifting', 'Room-to-Room Moving'],
            'Home Organization' => ['Closet Organization', 'Garage Organization', 'Kitchen Organization', 'General Home Organization'],
            'Minor Home Repairs' => ['General Handyman', 'Door Adjustment', 'Minor Wall Repair', 'Cabinet Hardware Installation', 'Grab Bar Installation', 'Childproofing'],
        ]],
        'products' => ['Products', 'product-commerce', [
            'Electronics' => ['TVs and Displays', 'Computers', 'Tablets', 'Mobile Phones', 'Audio', 'Cameras', 'Gaming', 'Networking', 'Electronic Accessories'],
            'Home and Kitchen' => ['Furniture', 'Home Decor', 'Kitchen', 'Bedding', 'Bathroom', 'Storage and Organization', 'Cleaning Supplies', 'Household Supplies'],
            'Appliances' => ['Major Appliances', 'Small Appliances', 'Kitchen Appliances', 'Laundry Appliances', 'Heating and Cooling', 'Appliance Parts'],
            'Tools and Hardware' => ['Hand Tools', 'Power Tools', 'Tool Accessories', 'Fasteners', 'Mounting Hardware', 'Electrical Supplies', 'Plumbing Supplies', 'Safety Equipment'],
            'Smart Home and Security' => ['Security Cameras', 'Video Doorbells', 'Smart Locks', 'Smart Lighting', 'Smart Thermostats', 'Sensors', 'Home Automation'],
            'Lighting' => ['Ceiling Lights', 'Chandeliers', 'Ceiling Fans', 'Wall Lights', 'Lamps', 'Light Bulbs', 'Lighting Accessories'],
            'Office' => ['Office Furniture', 'Office Electronics', 'Printers and Scanners', 'Office Supplies', 'Shipping Supplies', 'Presentation Equipment'],
            'Clothing and Accessories' => ['Men', 'Women', 'Children', 'Shoes', 'Bags', 'Jewelry', 'Fashion Accessories'],
            'Health and Personal Care' => ['Personal Care', 'Mobility and Accessibility', 'Medical Supplies', 'Fitness', 'Wellness'],
            'Sports and Outdoors' => ['Exercise and Fitness', 'Team Sports', 'Outdoor Recreation', 'Cycling', 'Sports Accessories'],
            'Automotive' => ['Automotive Parts', 'Automotive Accessories', 'Automotive Tools and Equipment', 'Car Electronics'],
            'Baby and Kids' => ['Baby Products', 'Toys', 'Kids Furniture', 'School Supplies', 'Child Safety'],
            'Pet Supplies' => ['Dog Supplies', 'Cat Supplies', 'Pet Furniture', 'Pet Care', 'Other Pet Supplies'],
            'Business and Industrial' => ['Commercial Equipment', 'Hospitality Equipment', 'Retail Equipment', 'Warehouse Supplies', 'Packaging', 'Signage', 'Professional Supplies'],
        ]],
        'projects' => ['Projects', 'project-delivery', [
            'Social' => ['Education', 'Employment and Skills', 'Community Development', 'Immigration and Integration', 'Accessibility and Inclusion', 'Youth Programs', 'Senior Engagement', 'Cultural Programs', 'Civic Participation', 'Environmental Initiatives', 'Public Awareness', 'Technology Access'],
            'Charity' => ['Emergency Medical Care', 'Treatment Assistance', 'Medication Assistance', 'Rehabilitation Assistance', 'Medical Equipment Assistance', 'Critical Illness Support', 'Accident Recovery', 'Maternal and Child Health', 'Emergency Food Assistance', 'Emergency Shelter', 'Disaster Relief', 'Lifesaving Humanitarian Aid'],
            'Business' => ['New Business', 'Product Development', 'Product Launch', 'Service Development', 'Service Launch', 'Marketing Campaign', 'Sales Campaign', 'Software Development', 'Website Development', 'Mobile App Development', 'Hardware Development', 'Research and Development', 'Business Automation', 'Internal Operations', 'Client Delivery', 'Commercial Installation', 'Expansion', 'Partnership'],
        ]],
    ];

    public static function getGroups(): array
    {
        return ['cataloging'];
    }

    public function load(ObjectManager $manager): void
    {
        if (!$manager instanceof EntityManagerInterface) {
            throw new \RuntimeException('Doctrine entity manager is required to load catalog fixtures.');
        }

        $connection = $manager->getConnection();
        $codes = array_keys(self::TREES);
        $placeholders = implode(', ', array_fill(0, count($codes), '?'));

        $connection->executeStatement(
            sprintf('DELETE FROM category_projection WHERE id IN (SELECT category.id::text FROM category JOIN catalog ON catalog.id = category.catalog_id WHERE catalog.object_code IN (%s))', $placeholders),
            $codes,
        );
        $connection->executeStatement(
            sprintf('DELETE FROM category WHERE catalog_id IN (SELECT id FROM catalog WHERE object_code IN (%s))', $placeholders),
            $codes,
        );
        $connection->executeStatement(
            sprintf('DELETE FROM catalog WHERE object_code IN (%s)', $placeholders),
            $codes,
        );

        foreach (self::TREES as $code => [$name, $purpose, $branches]) {
            $catalog = new CatalogCatalogEntity($code, $name, $purpose);
            $manager->persist($catalog);
            $manager->flush();

            $root = $this->category($catalog, $name, $code, $code, 0);
            $manager->persist($root);
            $manager->flush();
            $this->projection($manager, $root);

            foreach ($branches as $branchName => $leaves) {
                $branchSlug = $this->slug($branchName);
                $branch = $this->category($catalog, $branchName, $branchSlug, $code.'.'.$this->path($branchSlug), 1, (string) $root->getId());
                $manager->persist($branch);
                $manager->flush();
                $this->projection($manager, $branch);

                foreach ($leaves as $leafName) {
                    $leafSlug = $this->slug($leafName);
                    $leaf = $this->category($catalog, $leafName, $leafSlug, $branch->getPath().'.'.$this->path($leafSlug), 2, (string) $branch->getId());
                    $manager->persist($leaf);
                    $manager->flush();
                    $this->projection($manager, $leaf);
                }
            }
        }

        $manager->flush();
    }

    private function category(CatalogCatalogEntity $catalog, string $name, string $slug, string $path, int $depth, ?string $parentId = null): CatalogCategoryEntity
    {
        $category = new CatalogCategoryEntity($catalog, $name, $slug, $path, $depth, $parentId, 'en', 'default');
        $category->setWorkflowState('published');
        $category->setPublished(true);
        $category->setPublishedAt(new \DateTimeImmutable());

        return $category;
    }

    private function projection(ObjectManager $manager, CatalogCategoryEntity $category): void
    {
        $projection = new CatalogCategoryProjectionEntity((string) $category->getId());
        $projection->setSlug($category->getSlug());
        $projection->setName($category->getName());
        $projection->setParentId($category->getParentId());
        $projection->setPath($category->getPath());
        $projection->setLocale($category->getLocale());
        $projection->setTenant($category->getTenant());
        $projection->setWorkflowState('published');
        $projection->setPublished(true);
        $projection->setPublishedAt($category->getPublishedAt());
        $projection->setUpdatedAt(new \DateTimeImmutable());
        $manager->persist($projection);
    }

    private function slug(string $value): string
    {
        return substr(strtolower(trim((string) preg_replace('/[^a-z0-9]+/i', '-', $value), '-')), 0, 36);
    }

    private function path(string $slug): string
    {
        return str_replace('-', '_', $slug);
    }
}
