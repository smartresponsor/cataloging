<?php

declare(strict_types=1);

namespace App\Cataloging\Tests\Service;

use App\Cataloging\Service\CatalogReadOptimizerService;
use App\Cataloging\ServiceInterface\CatalogCategoryProjectionReadServiceInterface;
use App\Cataloging\ValueObject\CategoryProjectionCriteria;
use PHPUnit\Framework\TestCase;

final class CatalogReadOptimizerServiceTest extends TestCase
{
    public function testTreeIsProjectionBackedAndCached(): void
    {
        $calls = 0;
        $readService = new class($calls) implements CatalogCategoryProjectionReadServiceInterface {
            public function __construct(private int &$calls)
            {
            }

            public function list(?CategoryProjectionCriteria $criteria = null): array
            {
                return [];
            }

            public function tree(?CategoryProjectionCriteria $criteria = null): array
            {
                ++$this->calls;

                return [[
                    'id' => 'cat-1',
                    'slug' => 'root',
                    'nameEntity' => 'Root',
                    'parent_id' => null,
                    'path' => 'root',
                    'locale' => 'en',
                    'tenant' => 'default',
                    'workflow_state' => 'published',
                    'published' => true,
                    'published_at' => '2025-11-02 00:00:00',
                    'updated_at' => '2025-11-02 00:00:00',
                ]];
            }

            public function findOne(string $id): ?array
            {
                return null;
            }
        };

        $optimizer = new CatalogReadOptimizerService($readService);
        $criteria = CategoryProjectionCriteria::fromArray(['published' => true]);
        $first = $optimizer->getTree($criteria);
        $second = $optimizer->getTree($criteria);

        self::assertCount(1, $first);
        self::assertSame('default', $first[0]['channel']);
        self::assertSame($first, $second);
        self::assertSame(1, $calls);
        self::assertSame(['hit' => 1, 'miss' => 1, 'size' => 1], $optimizer->stats());
    }
}
