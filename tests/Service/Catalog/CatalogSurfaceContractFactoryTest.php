<?php

declare(strict_types=1);

namespace App\Cataloging\Tests\Service\Catalog;

use App\Cataloging\Service\Catalog\CatalogContractFactory;
use PHPUnit\Framework\TestCase;

final class CatalogSurfaceContractFactoryTest extends TestCase
{
    public function testIndexExposesFourBusinessCatalogsFromMarketplaceTree(): void
    {
        $factory = new CatalogContractFactory();
        $surface = $factory->create('catalog', $this->tree());

        $sections = $surface->slots['main.body']['sections'] ?? [];
        self::assertCount(1, $sections);

        $cards = $sections[0]['cards'] ?? [];
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
        self::assertSame('Appliance Installation', $surface->slots['main.body']['title']);
        self::assertSame('task', strtolower((string) ($surface->slots['right.panel']['stats'][1]['value'] ?? '')));
        self::assertSame('https://example.test/appliance.jpg', $surface->slots['main.body']['imageUrl']);

        $breadcrumbs = $surface->slots['main.body']['breadcrumbs'] ?? [];
        self::assertSame(
            ['Marketplace', 'Task Catalog', 'Appliance Installation'],
            array_column($breadcrumbs, 'title'),
        );

        $actions = $surface->slots['right.panel']['actions'] ?? [];
        self::assertSame('Browse task requests', $actions[0]['title']);
        self::assertSame('/catalog/?q=task', $actions[0]['url']);
    }

    public function testCatalogDetailShowsImmediateChildrenWithInheritedKind(): void
    {
        $factory = new CatalogContractFactory();
        $surface = $factory->createDetail('catalog', $this->tree(), 'task-catalog');

        self::assertNotNull($surface);
        $cards = $surface->slots['main.body']['sections'][0]['cards'] ?? [];
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
