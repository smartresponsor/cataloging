<?php

declare(strict_types=1);

namespace App\Cataloging\Tests\Service;

use App\Cataloging\Service\Catalog\CatalogSurfaceContractFactory;
use PHPUnit\Framework\TestCase;

final class CatalogSurfaceContractFactoryTest extends TestCase
{
    public function testCreateUsesCanonicalCatalogTemplatePath(): void
    {
        $factory = new CatalogSurfaceContractFactory();
        $surface = $factory->create('catalog-token', [
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

        self::assertSame('catalog', $surface->word);
        self::assertSame('base', $surface->view);
        self::assertSame('catalog/index.html.twig', $surface->templateName());
        self::assertArrayHasKey('top.search', $surface->slotMap);
        self::assertArrayHasKey('main.body', $surface->toTemplateContext()['slots']);
        self::assertArrayHasKey('right.panel', $surface->toTemplateContext()['slots']);
    }
}
