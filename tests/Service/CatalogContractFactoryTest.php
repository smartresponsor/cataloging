<?php

declare(strict_types=1);

namespace App\Cataloging\Tests\Service;

use App\Cataloging\Service\Catalog\CatalogContractFactory;
use PHPUnit\Framework\TestCase;

final class CatalogContractFactoryTest extends TestCase
{
    public function testCreateUsesCanonicalCatalogTemplatePath(): void
    {
        $factory = new CatalogContractFactory();
        $contract = $factory->create('catalog-token', [
            [
                'id' => 1,
                'slug' => 'electronics',
                'nameEntity' => 'Electronics',
                'locale' => 'en',
                'tenant' => 'main',
                'workflow_state' => 'published',
                'published' => true,
                'path' => '/electronics',
            ],
        ]);

        self::assertSame('catalog', $contract->word);
        self::assertSame('index', $contract->view);
        self::assertSame('@Interfacing/catalog/index.html.twig', $contract->templateName());
        self::assertArrayHasKey('top.search', $contract->slotMap);
        self::assertArrayHasKey('main.body', $contract->toTemplateContext()['slots']);
        self::assertArrayHasKey('right.panel', $contract->toTemplateContext()['slots']);
    }

    public function testCreateBuildsFourBusinessCatalogCards(): void
    {
        $factory = new CatalogContractFactory();
        $tree = [[
            'id' => 'root',
            'slug' => 'marketplace',
            'nameEntity' => 'Marketplace',
            'published' => true,
            'children' => [
                $this->catalogNode('task', 'Task Catalog', 'task.jpg'),
                $this->catalogNode('order', 'Order Catalog', 'order.jpg'),
                $this->catalogNode('product', 'Product Catalog', 'product.jpg'),
                $this->catalogNode('service', 'Service Catalog', 'service.jpg'),
            ],
        ]];

        $contract = $factory->create('catalog', $tree);
        $mainBody = $contract->slots['main.body'] ?? null;
        self::assertIsArray($mainBody);
        $sections = $mainBody['sections'] ?? null;
        self::assertIsArray($sections);
        $firstSection = $sections[0] ?? null;
        self::assertIsArray($firstSection);
        $cards = $firstSection['cards'] ?? null;
        self::assertIsArray($cards);

        self::assertSame(['Task Catalog', 'Order Catalog', 'Product Catalog', 'Service Catalog'], array_column($cards, 'title'));
        self::assertSame(['task.jpg', 'order.jpg', 'product.jpg', 'service.jpg'], array_column($cards, 'imageUrl'));
    }

    public function testCreateDetailInheritsCatalogKindAndBuildsBreadcrumbs(): void
    {
        $factory = new CatalogContractFactory();
        $tree = [[
            'id' => 'root',
            'slug' => 'marketplace',
            'nameEntity' => 'Marketplace',
            'published' => true,
            'children' => [[
                'id' => 'task',
                'slug' => 'task-catalog',
                'nameEntity' => 'Task Catalog',
                'published' => true,
                'children' => [[
                    'id' => 'appliance',
                    'slug' => 'appliance-installation',
                    'nameEntity' => 'Appliance Installation',
                    'icon_url' => 'appliance.jpg',
                    'published' => true,
                    'children' => [[
                        'id' => 'dishwasher',
                        'slug' => 'dishwasher-installation',
                        'nameEntity' => 'Dishwasher Installation',
                        'published' => true,
                        'children' => [],
                    ]],
                ]],
            ]],
        ]];

        $contract = $factory->createDetail('catalog', $tree, 'appliance-installation');

        self::assertNotNull($contract);
        $mainBody = $contract->slots['main.body'] ?? null;
        $rightPanel = $contract->slots['right.panel'] ?? null;
        self::assertIsArray($mainBody);
        self::assertIsArray($rightPanel);
        self::assertSame('Appliance Installation', $mainBody['title']);
        self::assertSame('appliance.jpg', $mainBody['imageUrl']);

        $stats = $rightPanel['stats'] ?? null;
        self::assertIsArray($stats);
        self::assertIsArray($stats[1] ?? null);
        self::assertSame('Task', $stats[1]['value']);

        $breadcrumbs = $mainBody['breadcrumbs'] ?? null;
        self::assertIsArray($breadcrumbs);
        self::assertSame(['Marketplace', 'Task Catalog', 'Appliance Installation'], array_column($breadcrumbs, 'title'));

        $sections = $mainBody['sections'] ?? null;
        self::assertIsArray($sections);
        self::assertIsArray($sections[0] ?? null);
        $cards = $sections[0]['cards'] ?? null;
        self::assertIsArray($cards);
        self::assertIsArray($cards[0] ?? null);
        self::assertSame('task', $cards[0]['kind']);

        $actions = $rightPanel['actions'] ?? null;
        self::assertIsArray($actions);
        self::assertIsArray($actions[0] ?? null);
        self::assertSame('Browse task requests', $actions[0]['title']);
    }

    public function testCreateDetailReturnsNullForUnknownSlug(): void
    {
        self::assertNull((new CatalogContractFactory())->createDetail('catalog', [], 'missing'));
    }

    /** @return array<string, mixed> */
    private function catalogNode(string $id, string $name, string $image): array
    {
        return [
            'id' => $id,
            'slug' => $id.'-catalog',
            'nameEntity' => $name,
            'icon_url' => $image,
            'published' => true,
            'children' => [[
                'id' => $id.'-child',
                'slug' => $id.'-child',
                'nameEntity' => ucfirst($id).' Child',
                'published' => true,
                'children' => [],
            ]],
        ];
    }
}
