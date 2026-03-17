<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Tests\Category\Importer;

use App\Importer\CategoryNdjsonImporter;
use App\ServiceInterface\CatalogCategoryInterface;
use PHPUnit\Framework\TestCase;

final class CategoryNdjsonImporterTest extends TestCase
{
    public function testImportPersistsCategoryAndLinkRows(): void
    {
        $path = sys_get_temp_dir().'/category-import-'.uniqid('', true).'.ndjson';
        file_put_contents($path, implode('
', [
            json_encode(['type' => 'category', 'taxonomyId' => 'catalog', 'parentId' => null, 'name' => ['en' => 'Root'], 'slug' => ['en' => 'root'], 'meta' => ['visible' => true]], JSON_THROW_ON_ERROR),
            json_encode(['type' => 'link', 'categoryId' => 'cat-1', 'targetDomain' => 'product', 'targetClass' => 'Product', 'targetId' => 'sku-1'], JSON_THROW_ON_ERROR),
        ]).'
');

        $calls = [];
        $service = new class($calls) implements CatalogCategoryInterface {
            public function __construct(private array &$calls)
            {
            }

            public function create(string $taxonomyId, ?string $parentId, array $name, array $slug, array $meta = []): array
            {
                $this->calls[] = ['create', $taxonomyId, $parentId, $name, $slug, $meta];

                return ['id' => 'cat-1'];
            }

            public function move(string $categoryId, ?string $newParentId, int $newOrder): array
            {
                return [];
            }

            public function attach(string $categoryId, string $targetDomain, string $targetClass, string $targetId): void
            {
                $this->calls[] = ['attach', $categoryId, $targetDomain, $targetClass, $targetId];
            }

            public function detach(string $categoryId, string $targetDomain, string $targetClass, string $targetId): void
            {
            }

            public function resolve(string $taxonomyCode, string $targetDomain, string $targetId, string $locale): array
            {
                return [];
            }
        };

        $importer = new CategoryNdjsonImporter($service);
        $result = $importer->import($path, false);

        self::assertSame(2, $result['ok']);
        self::assertSame(0, $result['fail']);
        self::assertSame([
            ['create', 'catalog', null, ['en' => 'Root'], ['en' => 'root'], ['visible' => true]],
            ['attach', 'cat-1', 'product', 'Product', 'sku-1'],
        ], $calls);
    }

    public function testImportCountsWarningsAndInvalidRowsOnDryRun(): void
    {
        $path = sys_get_temp_dir().'/category-import-'.uniqid('', true).'.ndjson';
        file_put_contents($path, implode('
', [
            json_encode(['type' => 'taxonomy', 'taxonomyId' => 'catalog'], JSON_THROW_ON_ERROR),
            json_encode(['type' => 'unknown'], JSON_THROW_ON_ERROR),
            'not-json',
        ]).'
');

        $service = new class implements CatalogCategoryInterface {
            public function create(string $taxonomyId, ?string $parentId, array $name, array $slug, array $meta = []): array
            {
                return [];
            }

            public function move(string $categoryId, ?string $newParentId, int $newOrder): array
            {
                return [];
            }

            public function attach(string $categoryId, string $targetDomain, string $targetClass, string $targetId): void
            {
            }

            public function detach(string $categoryId, string $targetDomain, string $targetClass, string $targetId): void
            {
            }

            public function resolve(string $taxonomyCode, string $targetDomain, string $targetId, string $locale): array
            {
                return [];
            }
        };

        $importer = new CategoryNdjsonImporter($service);
        $result = $importer->import($path, true);

        self::assertSame(0, $result['ok']);
        self::assertSame(2, $result['fail']);
        self::assertSame(1, $result['warnings']);
        self::assertContains('Taxonomy rows are metadata-only in importer', $result['report']);
        self::assertContains('Unknown type: unknown', $result['report']);
        self::assertContains('Invalid row', $result['report']);
    }
}
