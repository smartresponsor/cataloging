<?php

declare(strict_types=1);

namespace App\Tests\Category\Admin;

use App\Controller\Admin\CategoryMoveController;
use App\Service\CatalogCategoryMoveInterface;
use App\Service\MovePolicy;
use PHPUnit\Framework\TestCase;

final class CategoryMoveControllerTest extends TestCase
{
    public function testMovePassesCanonicalPayloadToService(): void
    {
        $captured = [];

        $service = new class($captured) implements CatalogCategoryMoveInterface {
            public array $captured;

            public function __construct(array &$captured)
            {
                $this->captured = &$captured;
            }

            public function move(string $nodeId, string $newParentId, string $treeId, string $policy, bool $dryRun = false, ?string $locale = null): array
            {
                $this->captured = compact('nodeId', 'newParentId', 'treeId', 'policy', 'dryRun', 'locale');

                return [2, [['from' => '/old', 'to' => '/new']]];
            }
        };

        $controller = new CategoryMoveController($service);
        $result = $controller->move([
            'nodeId' => 'cat-1',
            'newParentId' => 'cat-root',
            'treeId' => 'catalog',
            'policy' => MovePolicy::PreserveSlug,
            'dryRun' => true,
            'locale' => 'en',
        ]);

        self::assertTrue($result['ok']);
        self::assertSame(2, $result['changedCount']);
        self::assertSame('cat-1', $captured['nodeId']);
        self::assertSame('cat-root', $captured['newParentId']);
        self::assertSame('catalog', $captured['treeId']);
        self::assertSame(MovePolicy::PreserveSlug, $captured['policy']);
        self::assertTrue($captured['dryRun']);
        self::assertSame('en', $captured['locale']);
    }

    public function testMoveRejectsInvalidPolicy(): void
    {
        $service = new class implements CatalogCategoryMoveInterface {
            public function move(string $nodeId, string $newParentId, string $treeId, string $policy, bool $dryRun = false, ?string $locale = null): array
            {
                return [0, []];
            }
        };

        $controller = new CategoryMoveController($service);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('policy is invalid');

        $controller->move([
            'nodeId' => 'cat-1',
            'newParentId' => 'cat-root',
            'treeId' => 'catalog',
            'policy' => 'rewriteEverything',
        ]);
    }
}
