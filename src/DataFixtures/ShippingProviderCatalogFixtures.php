<?php

declare(strict_types=1);

namespace App\Cataloging\DataFixtures;

use App\Cataloging\Entity\Catalog\CatalogCatalogEntity;
use App\Cataloging\Entity\Catalog\CatalogCategoryEntity;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;

final class ShippingProviderCatalogFixtures extends Fixture implements FixtureGroupInterface
{
    private const PROVIDERS = [
        'ups' => ['label' => 'UPS', 'types' => [
            ['code' => 'ground', 'label' => 'Ground'],
            ['code' => 'express', 'label' => 'Express'],
            ['code' => 'overnight', 'label' => 'Overnight'],
        ]],
        'fedex' => ['label' => 'FedEx', 'types' => [
            ['code' => 'ground', 'label' => 'Ground'],
            ['code' => 'express', 'label' => 'Express'],
            ['code' => 'overnight', 'label' => 'Overnight'],
        ]],
        'usps' => ['label' => 'USPS', 'types' => [
            ['code' => 'standard', 'label' => 'Standard'],
        ]],
        'dhl' => ['label' => 'DHL', 'types' => [
            ['code' => 'economy', 'label' => 'Economy'],
            ['code' => 'express', 'label' => 'Express'],
        ]],
    ];

    public static function getGroups(): array
    {
        return ['cataloging'];
    }

    public function load(ObjectManager $manager): void
    {
        if (!$manager instanceof EntityManagerInterface) {
            throw new \RuntimeException('Doctrine entity manager is required.');
        }

        $catalog = $this->catalog($manager);
        foreach (self::PROVIDERS as $code => $definition) {
            $category = $manager->getRepository(CatalogCategoryEntity::class)->findOneBy([
                'catalog' => $catalog,
                'parentId' => null,
                'slug' => $code,
            ]);

            if (!$category instanceof CatalogCategoryEntity) {
                $category = new CatalogCategoryEntity($catalog, $definition['label'], $code, $code, 0, null, 'en', 'default');
                $manager->persist($category);
            }

            $category->setName($definition['label']);
            $category->setPath($code);
            $category->setDepth(0);
            $category->setMetadata(['schema' => 'catalog-category-types@1', 'types' => $definition['types']]);
            $category->setWorkflowState('published');
            $category->setPublished(true);
            if (null === $category->getPublishedAt()) {
                $category->setPublishedAt(new \DateTimeImmutable());
            }
        }

        $manager->flush();
    }

    private function catalog(EntityManagerInterface $manager): CatalogCatalogEntity
    {
        $id = $manager->getConnection()->fetchOne(
            'SELECT id FROM catalog WHERE object_code = :code AND tenant = :tenant ORDER BY id LIMIT 1',
            ['code' => 'shipping', 'tenant' => 'default'],
        );
        $catalog = false === $id ? null : $manager->find(CatalogCatalogEntity::class, (int) $id);
        if (!$catalog instanceof CatalogCatalogEntity) {
            $catalog = new CatalogCatalogEntity('shipping', 'Shipping', 'shipping-classification');
            $manager->persist($catalog);
            $manager->flush();
        }

        return $catalog;
    }
}
