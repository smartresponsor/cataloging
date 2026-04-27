<?php

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Tests\Category;

use App\Cataloging\RepositoryInterface\Catalog\CatalogCollectionProjectionRepositoryInterface;
use App\Cataloging\Service\CatalogCollectionService;
use App\Cataloging\Service\CollectionBuilder;
use App\Cataloging\Service\CollectionRuleEngine;
use PHPUnit\Framework\TestCase;

final class CatalogCollectionServiceTest extends TestCase
{
    public function testBuildReadsProjectionRowsInsteadOfHardcodedPayloads(): void
    {
        $repository = new class implements CatalogCollectionProjectionRepositoryInterface {
            public function list(): array
            {
                return [
                    [
                        'id' => 'p1',
                        'brand' => 'acme',
                        'price' => 79.0,
                        'stock' => 5,
                        'tag_set' => ['winter', 'sale'],
                    ],
                    [
                        'id' => 'p2',
                        'brand' => 'globex',
                        'price' => 129.0,
                        'stock' => 0,
                        'tag_set' => ['summer'],
                    ],
                ];
            }
        };

        $service = new CatalogCollectionService($repository, new CollectionBuilder(new CollectionRuleEngine()));
        $result = $service->build(['tag_set' => 'winter']);

        self::assertCount(1, $result);
        self::assertSame('p1', $result[0]['id']);
    }
}
