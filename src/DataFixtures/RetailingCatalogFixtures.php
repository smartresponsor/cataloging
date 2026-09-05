<?php

declare(strict_types=1);

namespace App\Cataloging\DataFixtures;

use App\Cataloging\Entity\Catalog\CatalogCatalogEntity;
use App\Cataloging\Entity\Catalog\CatalogCategoryEntity;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;

final class RetailingCatalogFixtures extends Fixture implements FixtureGroupInterface
{
    private const CATEGORIES = [
        'product' => 'Product',
        'service' => 'Service',
        'project' => 'Project',
        'task' => 'Task',
        'order' => 'Order',
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
            $fixtureMetadata = array_replace(self::METADATA[$code], $this->taxonomyMetadata($code));
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
        $existingTypes = is_array($existing['types'] ?? null) ? $existing['types'] : [];
        $fixtureTypes = is_array($fixture['types'] ?? null) ? $fixture['types'] : [];
        if ([] !== $existingTypes || [] !== $fixtureTypes) {
            $merged['types'] = $this->mergeTypesByCode($existingTypes, $fixtureTypes);
        }

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
            $combined['types'] = $this->mergeTypesByCode($existingTypes, $fixtureTypes);
            $support[$kind] = $combined;
        }

        $merged['support'] = $support;

        return $merged;
    }

    /**
     * @param array<int, mixed> $existingTypes
     * @param array<int, mixed> $fixtureTypes
     *
     * @return list<array<string, mixed>>
     */
    private function mergeTypesByCode(array $existingTypes, array $fixtureTypes): array
    {
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

                $current = $typesByCode[$code];
                $merged = array_replace($current, $type);
                $existingChildren = is_array($current['types'] ?? null) ? $current['types'] : [];
                $fixtureChildren = is_array($type['types'] ?? null) ? $type['types'] : [];
                if ([] !== $existingChildren || [] !== $fixtureChildren) {
                    $merged['types'] = $this->mergeTypesByCode($existingChildren, $fixtureChildren);
                }
                $typesByCode[$code] = $merged;
            }
        }

        return array_map(static fn (string $code): array => $typesByCode[$code], $order);
    }

    /** @return array<string, mixed> */
    private function taxonomyMetadata(string $code): array
    {
        $path = dirname(__DIR__, 2).'/resources/retailing/'.$code.'.json';
        $json = file_get_contents($path);
        if (false === $json) {
            throw new \RuntimeException(sprintf('Retailing taxonomy resource is missing: %s.', $path));
        }

        $metadata = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($metadata) || 'retailing-category@1' !== ($metadata['schema'] ?? null) || !is_array($metadata['types'] ?? null)) {
            throw new \RuntimeException(sprintf('Retailing taxonomy resource is invalid: %s.', $path));
        }

        return $metadata;
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
