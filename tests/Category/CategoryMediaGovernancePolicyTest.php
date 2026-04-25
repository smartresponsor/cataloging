<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Cataloging\Tests\Category;

use App\Cataloging\Policy\CategoryMediaGovernancePolicy;
use App\Cataloging\ValueObject\CatalogAuditContext;
use App\Cataloging\ValueObject\CatalogCategoryMediaBindingEntityScope;
use App\Cataloging\ValueObject\CatalogCategoryMediaBindingEntityState;
use App\Cataloging\ValueObject\CategoryMediaBindRequest;
use PHPUnit\Framework\TestCase;

final class CategoryMediaGovernancePolicyTest extends TestCase
{
    public function testAssertBindingAllowedAcceptsCanonicalMediaPayload(): void
    {
        $policy = new CategoryMediaGovernancePolicy();

        $policy->assertBindingAllowed(
            new CategoryMediaBindRequest(
                new CatalogCategoryMediaBindingEntityScope(
                    'binding-1',
                    'category-1',
                    'asset-1',
                    'primary',
                    ['storefront'],
                    ['en_US'],
                ),
                new CatalogCategoryMediaBindingEntityState(true, true, []),
                new CatalogAuditContext('operator-1', 'bind primary category image'),
            ),
        );

        self::addToAssertionCount(1);
    }

    public function testAssertBindingAllowedRejectsMissingChannels(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $policy = new CategoryMediaGovernancePolicy();
        $policy->assertBindingAllowed(
            new CategoryMediaBindRequest(
                new CatalogCategoryMediaBindingEntityScope(
                    'binding-1',
                    'category-1',
                    'asset-1',
                    'primary',
                    [],
                    ['en_US'],
                ),
                new CatalogCategoryMediaBindingEntityState(true, true, []),
                new CatalogAuditContext('operator-1', 'bind primary category image'),
            ),
        );
    }
}
