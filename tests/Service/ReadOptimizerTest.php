<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Service\ReadOptimizer;
use App\ServiceInterface\CategoryProjectionReadServiceInterface;
use PHPUnit\Framework\TestCase;

final class ReadOptimizerTest extends TestCase
{
    public function testTreeIsProjectionBackedAndCached(): void
    {
        $calls = 0;
        $readService = new class($calls) implements CategoryProjectionReadServiceInterface {
            public function __construct(private int &$calls)
            {
            }

            public function list(array $criteria = []): array
            {
                return [];
            }

            public function tree(array $criteria = []): array
            {
                ++$this->calls;

                return [[
                    'id' => 'cat-1',
                    'slug' => 'root',
                    'name' => 'Root',
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

        $optimizer = new ReadOptimizer($readService);
        $first = $optimizer->getTree(['published' => true]);
        $second = $optimizer->getTree(['published' => true]);

        self::assertCount(1, $first);
        self::assertSame('default', $first[0]['channel']);
        self::assertSame($first, $second);
        self::assertSame(1, $calls);
        self::assertSame(['hit' => 1, 'miss' => 1, 'size' => 1], $optimizer->stats());
    }
}
