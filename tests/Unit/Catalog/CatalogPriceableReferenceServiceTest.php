<?php

declare(strict_types=1);

namespace App\Cataloging\Tests\Unit\Catalog;

use App\Cataloging\Service\CatalogPriceableReferenceService;
use PHPUnit\Framework\TestCase;

/** Verifies the stable Cataloging-to-Pricing resource reference boundary. */
final class CatalogPriceableReferenceServiceTest extends TestCase
{
    public function testBuildsStableOpaqueReference(): void
    {
        self::assertSame(
            'catalog:record:variant-42',
            (new CatalogPriceableReferenceService())->forRecord(' variant-42 '),
        );
    }

    public function testRejectsEmptyIdentity(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        (new CatalogPriceableReferenceService())->forRecord(' ');
    }
}
