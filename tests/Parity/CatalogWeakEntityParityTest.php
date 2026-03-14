<?php

declare(strict_types=1);

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

namespace App\Tests\Parity;

use App\Entity\CategoryLink;
use App\Entity\CategoryRedirect;
use App\Entity\CategoryTaxonomy;
use App\Entity\ProjectionControlEntity;
use App\Entity\VirtualCategoryEntity;
use PHPUnit\Framework\TestCase;

final class CatalogWeakEntityParityTest extends TestCase
{
    public function testCategoryTaxonomyCarriesJsonFields(): void
    {
        $taxonomy = new CategoryTaxonomy(
            '01taxonomy00000000000000001',
            'catalog',
            ['en' => 'Catalog'],
            ['scope' => 'default']
        );

        self::assertSame('catalog', $taxonomy->code());
        self::assertSame(['en' => 'Catalog'], $taxonomy->name());
        self::assertSame(['scope' => 'default'], $taxonomy->rule());
    }

    public function testCategoryLinkKeepsTargetInformation(): void
    {
        $link = new CategoryLink(
            '01link000000000000000000001',
            '01taxonomy00000000000000001',
            '01category00000000000000001',
            'product',
            'ProductEntity',
            'prod-phone-001'
        );

        self::assertSame('product', $link->targetDomain());
        self::assertSame('ProductEntity', $link->targetClass());
        self::assertSame('prod-phone-001', $link->targetId());
    }

    public function testCategoryRedirectExposesFromAndTo(): void
    {
        $redirect = new CategoryRedirect(
            '01redirect0000000000000001',
            '/catalog/root/electronics/smartphones',
            '/catalog/root/electronics/phones'
        );

        self::assertSame('/catalog/root/electronics/smartphones', $redirect->frm());
        self::assertSame('/catalog/root/electronics/phones', $redirect->to());
    }

    public function testProjectionControlEntityTracksPauseState(): void
    {
        $projectionControl = new ProjectionControlEntity('category', false);
        self::assertFalse($projectionControl->paused());

        $projectionControl->setPaused(true);
        self::assertTrue($projectionControl->paused());
    }

    public function testVirtualCategoryEntityStoresRulePayload(): void
    {
        $virtualCategory = new VirtualCategoryEntity(
            '01virtual00000000000000001',
            'Featured phones',
            ['code' => 'featured-phone']
        );

        self::assertSame('Featured phones', $virtualCategory->getName());
        self::assertSame(['code' => 'featured-phone'], $virtualCategory->getRule());
    }
}
