<?php

declare(strict_types=1);

namespace App\Cataloging\Tests\Service\Catalog;

use App\Cataloging\Service\Catalog\CatalogContractFactory;
use App\Cataloging\Value\Surface\CatalogContract;
use PHPUnit\Framework\TestCase;

final class CatalogSurfaceContractFactoryTest extends TestCase
{
    public function testIndexExposesFourBusinessCatalogsFromMarketplaceTree(): void
    {
        $factory = new CatalogContractFactory();
        $surface = $factory->create('catalog', $this->tree());

        $mainBody = self::slot($surface, 'main.body');
        $sections = self::rowList($mainBody['sections'] ?? null);
        self::assertCount(1, $sections);

        $cards = self::rowList($sections[0]['cards'] ?? null);
        self::assertSame(
            ['Task Catalog', 'Order Catalog', 'Product Catalog', 'Service Catalog'],
            array_column($cards, 'title'),
        );
        self::assertSame(['task', 'order', 'product', 'service'], array_column($cards, 'kind'));
        self::assertSame('https://example.test/task.jpg', $cards[0]['imageUrl']);
        self::assertSame('2 categories', $cards[0]['itemCount']);
    }

    public function testDetailInheritsCatalogKindAndBuildsChildCards(): void
    {
        $factory = new CatalogContractFactory();
        $surface = $factory->createDetail('catalog', $this->tree(), 'appliance-installation');

        self::assertNotNull($surface);
        $mainBody = self::slot($surface, 'main.body');
        $rightPanel = self::slot($surface, 'right.panel');
        self::assertSame('Appliance Installation', $mainBody['title']);
        self::assertSame('https://example.test/appliance.jpg', $mainBody['imageUrl']);

        $stats = self::rowList($rightPanel['stats'] ?? null);
        self::assertSame('task', strtolower(self::stringValue($stats[1]['value'] ?? null)));

        $breadcrumbs = self::rowList($mainBody['breadcrumbs'] ?? null);
        self::assertSame(
            ['Marketplace', 'Task Catalog', 'Appliance Installation'],
            array_column($breadcrumbs, 'title'),
        );

        $actions = self::rowList($rightPanel['actions'] ?? null);
        self::assertSame('Browse task requests', $actions[0]['title']);
        self::assertSame('/catalog/?q=task', $actions[0]['url']);
    }

    public function testCatalogDetailShowsImmediateChildrenWithInheritedKind(): void
    {
        $factory = new CatalogContractFactory();
        $surface = $factory->createDetail('catalog', $this->tree(), 'task-catalog');

        self::assertNotNull($surface);
        $mainBody = self::slot($surface, 'main.body');
        $sections = self::rowList($mainBody['sections'] ?? null);
        $cards = self::rowList($sections[0]['cards'] ?? null);
        self::assertCount(2, $cards);
        self::assertSame(['task', 'task'], array_column($cards, 'kind'));
        self::assertSame(
            ['/catalog/category/appliance-installation', '/catalog/category/home-repair'],
            array_column($cards, 'href'),
        );
    }

    public function testUnknownSlugReturnsNullDetailSurface(): void
    {
        $factory = new CatalogContractFactory();

        self::assertNull($factory->createDetail('catalog', $this->tree(), 'missing-category'));
    }

    /** @return array<string, mixed> */
    private static function slot(CatalogContract $surface, string $name): array
    {
        $slot = $surface->slots[$name] ?? null;
        self::assertIsArray($slot);

        $normalized = [];
        foreach ($slot as $key => $value) {
            if (is_string($key)) {
                $normalized[$key] = $value;
            }
        }

        return $normalized;
    }

    /** @return list<array<string, mixed>> */
    private static function rowList(mixed $value): array
    {
        self::assertIsArray($value);
        $rows = [];
        foreach ($value as $row) {
            self::assertIsArray($row);
            $normalized = [];
            foreach ($row as $key => $item) {
                if (is_string($key)) {
                    $normalized[$key] = $item;
                }
            }
            $rows[] = $normalized;
        }

        return $rows;
    }

    private static function stringValue(mixed $value): string
    {
        self::assertIsScalar($value);

        return (string) $value;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function tree(): array
    {
        return [[
            'id' => 'root',
            'slug' => 'marketplace',
            'nameEntity' => 'Marketplace',
            'published' => true,
            'children' => [
                [
                    'id' => 'task',
                    'slug' => 'task-catalog',
                    'nameEntity' => 'Task Catalog',
                    'published' => true,
                    'icon_url' => 'https://example.test/task.jpg',
                    'children' => [
                        [
                            'id' => 'appliance',
                            'slug' => 'appliance-installation',
                            'nameEntity' => 'Appliance Installation',
                            'published' => true,
                            'icon_url' => 'https://example.test/appliance.jpg',
                            'children' => [],
                        ],
                        [
                            'id' => 'repair',
                            'slug' => 'home-repair',
                            'nameEntity' => 'Home Repair',
                            'published' => true,
                            'children' => [],
                        ],
                    ],
                ],
                [
                    'id' => 'order',
                    'slug' => 'order-catalog',
                    'nameEntity' => 'Order Catalog',
                    'published' => true,
                    'children' => [],
                ],
                [
                    'id' => 'product',
                    'slug' => 'product-catalog',
                    'nameEntity' => 'Product Catalog',
                    'published' => true,
                    'children' => [],
                ],
                [
                    'id' => 'service',
                    'slug' => 'service-catalog',
                    'nameEntity' => 'Service Catalog',
                    'published' => true,
                    'children' => [],
                ],
            ],
        ]];
    }
}
