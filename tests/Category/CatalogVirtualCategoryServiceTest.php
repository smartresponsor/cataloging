<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Tests\Category;

use App\RepositoryInterface\CatalogCollectionProjectionRepositoryInterface;
use App\RepositoryInterface\VirtualCategoryRepositoryInterface;
use App\Service\CatalogCollectionService;
use App\Service\CatalogVirtualCategoryService;
use App\Service\CategoryRuleEngine;
use App\Service\CollectionBuilder;
use PHPUnit\Framework\TestCase;

final class CatalogVirtualCategoryServiceTest extends TestCase
{
    public function testPreviewNormalizesRulesBeforeBuild(): void
    {
        $projectionRepository = new class implements CatalogCollectionProjectionRepositoryInterface {
            public function list(): array
            {
                return [
                    [
                        'id' => 'cat-1',
                        'color' => 'red',
                        'tags' => ['winter', 'sale'],
                    ],
                    [
                        'id' => 'cat-2',
                        'color' => 'blue',
                        'tags' => ['summer'],
                    ],
                ];
            }
        };

        $collectionService = new CatalogCollectionService(
            $projectionRepository,
            new CollectionBuilder(new CategoryRuleEngine()),
        );

        $virtualRepository = new class implements VirtualCategoryRepositoryInterface {
            public function findById(string $id): ?array
            {
                return null;
            }
        };

        $service = new CatalogVirtualCategoryService($collectionService, $virtualRepository);
        $result = $service->preview([
            100 => 'ignored',
            'color' => 'red',
            'tags' => ['winter', ['ignored']],
        ]);

        self::assertCount(1, $result);
        self::assertSame('cat-1', $result[0]['id']);
    }

    public function testApplyReturnsNullWhenVirtualCategoryDoesNotExist(): void
    {
        $projectionRepository = new class implements CatalogCollectionProjectionRepositoryInterface {
            public function list(): array
            {
                return [];
            }
        };

        $collectionService = new CatalogCollectionService(
            $projectionRepository,
            new CollectionBuilder(new CategoryRuleEngine()),
        );

        $virtualRepository = new class implements VirtualCategoryRepositoryInterface {
            public function findById(string $id): ?array
            {
                return null;
            }
        };

        $service = new CatalogVirtualCategoryService($collectionService, $virtualRepository);

        self::assertNull($service->apply('01HNOTFOUND0000000000000000'));
    }

    public function testApplyBuildsDataFromStoredRuleAndReturnsTotal(): void
    {
        $projectionRepository = new class implements CatalogCollectionProjectionRepositoryInterface {
            public function list(): array
            {
                return [
                    [
                        'id' => 'cat-1',
                        'color' => 'red',
                        'tags' => ['winter', 'sale'],
                    ],
                    [
                        'id' => 'cat-2',
                        'color' => 'blue',
                        'tags' => ['summer'],
                    ],
                ];
            }
        };

        $collectionService = new CatalogCollectionService(
            $projectionRepository,
            new CollectionBuilder(new CategoryRuleEngine()),
        );

        $virtualRepository = new class implements VirtualCategoryRepositoryInterface {
            public function findById(string $id): ?array
            {
                return [
                    'id' => $id,
                    'name' => 'Winter Red',
                    'rule' => [
                        'color' => 'red',
                        'tags' => ['winter'],
                    ],
                ];
            }
        };

        $service = new CatalogVirtualCategoryService($collectionService, $virtualRepository);
        $result = $service->apply('01HWINTERRED000000000000000');

        self::assertNotNull($result);
        self::assertSame('Winter Red', $result['name']);
        self::assertSame(1, $result['total']);
        self::assertCount(1, $result['data']);
        self::assertSame('cat-1', $result['data'][0]['id']);
    }
}
