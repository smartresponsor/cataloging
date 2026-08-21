<?php

declare(strict_types=1);

namespace App\Cataloging\DataFixtures;

use App\Cataloging\Entity\Catalog\CatalogCatalogEntity;
use App\Cataloging\Entity\Catalog\CatalogCategoryEntity;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;

final class RetailingCatalogFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    private const CATEGORIES = [
        'product' => 'Product',
        'service' => 'Service',
        'project' => 'Project',
        'task' => 'Task',
        'order' => 'Order',
    ];

    private const SOURCE_CATALOGS = [
        'product' => 'products',
        'service' => 'services',
        'project' => 'projects',
        'task' => 'services',
    ];

    private const METADATA = [
        'product' => [
            'schema' => 'retailing-category@1',
            'support' => [
                'return' => [
                    'label' => 'Return',
                    'types' => [
                        ['code' => 'damaged', 'label' => 'Damaged'],
                        ['code' => 'wrong_item', 'label' => 'Wrong Item'],
                        ['code' => 'not_as_described', 'label' => 'Not as Described'],
                        ['code' => 'missing_parts', 'label' => 'Missing Parts'],
                        ['code' => 'defective', 'label' => 'Defective'],
                        ['code' => 'other', 'label' => 'Other'],
                    ],
                ],
            ],
        ],
        'service' => [
            'schema' => 'retailing-category@1',
            'support' => [
                'dispute' => [
                    'label' => 'Dispute',
                    'types' => [
                        ['code' => 'quality', 'label' => 'Quality'],
                        ['code' => 'no_show', 'label' => 'No-show'],
                        ['code' => 'billing', 'label' => 'Billing'],
                        ['code' => 'property_damage', 'label' => 'Property Damage'],
                        ['code' => 'incomplete_work', 'label' => 'Incomplete Work'],
                        ['code' => 'other', 'label' => 'Other'],
                    ],
                ],
            ],
        ],
        'project' => ['schema' => 'retailing-category@1', 'support' => []],
        'task' => ['schema' => 'retailing-category@1', 'support' => []],
        'order' => ['schema' => 'retailing-category@1', 'support' => []],
    ];

    public static function getGroups(): array
    {
        return ['cataloging'];
    }

    public function getDependencies(): array
    {
        return [MultiCatalogFixtures::class];
    }

    public function load(ObjectManager $manager): void
    {
        if (!$manager instanceof EntityManagerInterface) {
            throw new \RuntimeException('Doctrine entity manager is required.');
        }

        $catalog = $this->catalog($manager);
        foreach (self::CATEGORIES as $code => $label) {
            $category = $manager->getRepository(CatalogCategoryEntity::class)->findOneBy([
                'catalog' => $catalog,
                'parentId' => null,
                'slug' => $code,
            ]);
            if (!$category instanceof CatalogCategoryEntity && 'product' === $code) {
                $category = $manager->getRepository(CatalogCategoryEntity::class)->findOneBy([
                    'catalog' => $catalog,
                    'parentId' => null,
                    'slug' => 'goods',
                ]);
            }
            if (!$category instanceof CatalogCategoryEntity) {
                $category = new CatalogCategoryEntity($catalog, $label, $code, $code, 0, null, 'en', 'default');
                $manager->persist($category);
            }
            $category->setName($label);
            $category->setSlug($code);
            $category->setPath($code);
            $category->setDepth(0);
            $fixtureMetadata = self::METADATA[$code];
            $sourceCatalog = self::SOURCE_CATALOGS[$code] ?? null;
            if (is_string($sourceCatalog)) {
                $fixtureMetadata['types'] = $this->typesFromCatalog($manager, $sourceCatalog);
                $fixtureMetadata['sourceCatalog'] = $sourceCatalog;
            }
            $category->setMetadata($this->mergeMetadata($category->getMetadata(), $fixtureMetadata));
            $category->setWorkflowState('published');
            $category->setPublished(true);
            if (null === $category->getPublishedAt()) {
                $category->setPublishedAt(new \DateTimeImmutable());
            }
        }

        $manager->flush();
    }

    /**
     * @param array<string, mixed> $existing
     * @param array<string, mixed> $fixture
     *
     * @return array<string, mixed>
     */
    private function mergeMetadata(array $existing, array $fixture): array
    {
        $merged = array_replace($existing, $fixture);
        $existingSupport = is_array($existing['support'] ?? null) ? $existing['support'] : [];
        $fixtureSupport = is_array($fixture['support'] ?? null) ? $fixture['support'] : [];
        $support = $existingSupport;

        foreach ($fixtureSupport as $kind => $definition) {
            if (!is_string($kind) || !is_array($definition)) {
                continue;
            }
            $current = is_array($support[$kind] ?? null) ? $support[$kind] : [];
            $combined = array_replace($current, $definition);
            $existingTypes = is_array($current['types'] ?? null) ? $current['types'] : [];
            $fixtureTypes = is_array($definition['types'] ?? null) ? $definition['types'] : [];
            $typesByCode = [];
            $order = [];
            foreach ([$existingTypes, $fixtureTypes] as $types) {
                foreach ($types as $type) {
                    if (!is_array($type)) {
                        continue;
                    }
                    $code = strtolower(trim((string) ($type['code'] ?? '')));
                    if ('' === $code) {
                        continue;
                    }
                    if (!isset($typesByCode[$code])) {
                        $order[] = $code;
                        $typesByCode[$code] = $type;
                        continue;
                    }
                    $typesByCode[$code] = array_replace($typesByCode[$code], $type);
                }
            }
            $combined['types'] = array_map(static fn (string $code): array => $typesByCode[$code], $order);
            $support[$kind] = $combined;
        }

        $merged['support'] = $support;

        return $merged;
    }

    /** @return list<array<string, mixed>> */
    private function typesFromCatalog(EntityManagerInterface $manager, string $catalogCode): array
    {
        $rows = $manager->getConnection()->fetchAllAssociative(
            <<<'SQL'
SELECT category.id, category.parent_id, category.slug, category.name_entity, category.depth, category.path
FROM category
JOIN catalog ON catalog.id = category.catalog_id
WHERE catalog.object_code = :catalogCode
  AND catalog.tenant = :tenant
  AND category.published = TRUE
  AND category.workflow_state = 'published'
ORDER BY category.depth ASC, category.path ASC
SQL,
            ['catalogCode' => $catalogCode, 'tenant' => 'default'],
        );

        $nodes = [];
        foreach ($rows as $row) {
            if ((int) $row['depth'] <= 0) {
                continue;
            }
            $id = (string) $row['id'];
            $nodes[$id] = [
                'parentId' => null === $row['parent_id'] ? null : (string) $row['parent_id'],
                'type' => [
                    'code' => (string) $row['slug'],
                    'label' => (string) $row['name_entity'],
                    'sourceCategoryId' => $id,
                ],
            ];
        }

        foreach ($nodes as $id => $node) {
            $parentId = $node['parentId'];
            if (null === $parentId || !isset($nodes[$parentId])) {
                continue;
            }
            $nodes[$parentId]['type']['types'] ??= [];
            $nodes[$parentId]['type']['types'][] = &$nodes[$id]['type'];
        }

        $types = [];
        foreach ($nodes as $node) {
            $parentId = $node['parentId'];
            if (null === $parentId || !isset($nodes[$parentId])) {
                $types[] = $node['type'];
            }
        }

        return $types;
    }

    private function catalog(EntityManagerInterface $manager): CatalogCatalogEntity
    {
        $id = $manager->getConnection()->fetchOne(
            'SELECT id FROM catalog WHERE object_code = :code AND tenant = :tenant ORDER BY id LIMIT 1',
            ['code' => 'retailing', 'tenant' => 'default'],
        );
        $catalog = false === $id ? null : $manager->find(CatalogCatalogEntity::class, (int) $id);
        if (!$catalog instanceof CatalogCatalogEntity) {
            $catalog = new CatalogCatalogEntity('retailing', 'Retailing', 'retailing-classification');
            $manager->persist($catalog);
            $manager->flush();
        }

        return $catalog;
    }
}
