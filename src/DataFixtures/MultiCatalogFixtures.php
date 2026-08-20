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
    private const PUBLISHED_SERVICE_BRANCHES = [
        'Appliance Installation',
        'Ceiling Fan Installation',
        'Furniture Assembly',
        'Home Cleaning',
        'Home Organization',
        'Lighting Installation',
        'Mirror Hanging',
        'Picture Hanging',
        'Security Device Installation',
        'Shelf Mounting',
        'TV Mounting',
        'Window Treatment Installation',
    ];

    /** @var array<string, true> */
    private const PUBLISHED_SERVICE_LEAF_SLUGS = [
        'standard-home-cleaning' => true,
        'deep-cleaning' => true,
        'standard-furniture-assembly' => true,
        'standard-tv-mounting' => true,
        'gallery-wall-installation' => true,
        'heavy-mirror-hanging' => true,
        'floating-shelf-installation' => true,
        'curtain-rod-installation' => true,
        'chandelier-installation' => true,
        'ceiling-fan-replacement' => true,
        'security-camera-installation' => true,
        'video-doorbell-installation' => true,
        'door-lock-replacement' => true,
        'smart-lock-installation' => true,
        'dishwasher-installation' => true,
        'closet-organization' => true,
        'kitchen-organization' => true,
    ];

    private const SERVICE_LEAF_DISPLAY_NAMES = [
        'standard-home-cleaning' => 'House Cleaning',
        'standard-furniture-assembly' => 'Furniture Assembly',
        'standard-tv-mounting' => 'TV Mounting',
    ];

    private const TREES = [
        'services' => ['Services', 'service-discovery', [
            'Dispute' => [],
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
            'Security Device Installation' => ['Security Camera Installation', 'Video Doorbell Installation', 'Door Lock Replacement', 'Smart Lock Installation', 'Home Security System Setup'],
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
        'leads' => ['Leads', 'lead-discovery', [
            'Dispute' => ['Invalid', 'Duplicate', 'Wrong Category', 'Outside Service Area', 'Fraud or Spam', 'Billing Dispute'],
        ]],
        'products' => ['Products', 'product-commerce', [
            'Return' => [],
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

        foreach (self::TREES as $code => [$name, $purpose, $branches]) {
            $catalog = $this->adoptCatalog($manager, $code, $name, $purpose);
            $seenCategoryIds = [];

            $root = $this->adoptCategory($manager, $catalog, $name, $code, $code, 0, null, true);
            $seenCategoryIds[$root->getId()] = true;
            $this->projection($manager, $root);

            foreach ($branches as $branchName => $leaves) {
                $published = 'services' !== $code || in_array($branchName, self::PUBLISHED_SERVICE_BRANCHES, true);
                $branchSlug = $this->slug($branchName);
                $branch = $this->adoptCategory($manager, $catalog, $branchName, $branchSlug, $code.'.'.$this->path($branchSlug), 1, (string) $root->getId(), $published);
                $seenCategoryIds[$branch->getId()] = true;
                $this->projection($manager, $branch);

                foreach ($leaves as $leafName) {
                    $leafSlug = $this->slug($leafName);
                    $leafPublished = 'services' !== $code || ($published && isset(self::PUBLISHED_SERVICE_LEAF_SLUGS[$leafSlug]));
                    $displayName = 'services' === $code ? (self::SERVICE_LEAF_DISPLAY_NAMES[$leafSlug] ?? $leafName) : $leafName;
                    $leaf = $this->adoptCategory($manager, $catalog, $displayName, $leafSlug, $branch->getPath().'.'.$this->path($leafSlug), 2, (string) $branch->getId(), $leafPublished);
                    $seenCategoryIds[$leaf->getId()] = true;
                    $this->projection($manager, $leaf);
                }
            }

            if ('services' === $code) {
                $this->unpublishUnknownServiceCategories($manager, $catalog, $seenCategoryIds);
            }
        }

        $manager->flush();
    }

    private function adoptCatalog(EntityManagerInterface $manager, string $code, string $name, string $purpose): CatalogCatalogEntity
    {
        $id = $manager->getConnection()->fetchOne(
            'SELECT id FROM catalog WHERE object_code = :code AND tenant = :tenant ORDER BY id LIMIT 1',
            ['code' => $code, 'tenant' => 'default'],
        );
        $catalog = false === $id ? null : $manager->find(CatalogCatalogEntity::class, (int) $id);
        if (!$catalog instanceof CatalogCatalogEntity) {
            $catalog = new CatalogCatalogEntity($code, $name, $purpose);
            $manager->persist($catalog);
            $manager->flush();
        } else {
            $catalog->setName($name);
            $catalog->setPurpose($purpose);
        }

        return $catalog;
    }

    private function adoptCategory(EntityManagerInterface $manager, CatalogCatalogEntity $catalog, string $name, string $slug, string $path, int $depth, ?string $parentId = null, bool $published = true): CatalogCategoryEntity
    {
        $category = $manager->getRepository(CatalogCategoryEntity::class)->findOneBy([
            'catalog' => $catalog,
            'parentId' => null === $parentId ? null : (int) $parentId,
            'slug' => $slug,
        ]);
        if (!$category instanceof CatalogCategoryEntity) {
            $category = new CatalogCategoryEntity($catalog, $name, $slug, $path, $depth, $parentId, 'en', 'default');
            $manager->persist($category);
            $manager->flush();
        } else {
            $category->setName($name);
            $category->setSlug($slug);
            $category->setParentId($parentId);
            $category->setPath($path);
            $category->setDepth($depth);
            $category->setLocale('en');
            $category->setTenant('default');
        }

        $category->setWorkflowState($published ? 'published' : 'draft');
        $category->setPublished($published);
        if ($published && null === $category->getPublishedAt()) {
            $category->setPublishedAt(new \DateTimeImmutable());
        }

        return $category;
    }

    private function projection(EntityManagerInterface $manager, CatalogCategoryEntity $category): void
    {
        $projection = $manager->getRepository(CatalogCategoryProjectionEntity::class)->find((string) $category->getId());
        if (!$projection instanceof CatalogCategoryProjectionEntity) {
            $projection = new CatalogCategoryProjectionEntity((string) $category->getId());
        }
        $projection->setSlug($category->getSlug());
        $projection->setName($category->getName());
        $projection->setParentId($category->getParentId());
        $projection->setPath($category->getPath());
        $projection->setLocale($category->getLocale());
        $projection->setTenant($category->getTenant());
        $projection->setWorkflowState($category->getWorkflowState());
        $projection->setPublished($category->isPublished());
        $projection->setPublishedAt($category->getPublishedAt());
        $projection->setUpdatedAt(new \DateTimeImmutable());
        $manager->persist($projection);
    }

    /** @param array<int, true> $seenCategoryIds */
    private function unpublishUnknownServiceCategories(EntityManagerInterface $manager, CatalogCatalogEntity $catalog, array $seenCategoryIds): void
    {
        $categories = $manager->getRepository(CatalogCategoryEntity::class)->findBy(['catalog' => $catalog]);
        foreach ($categories as $category) {
            if (!$category instanceof CatalogCategoryEntity || isset($seenCategoryIds[$category->getId()])) {
                continue;
            }

            $category->setWorkflowState('draft');
            $category->setPublished(false);
            $category->setPublishedAt(null);
            $manager->persist($category);
            $this->projection($manager, $category);
        }
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
