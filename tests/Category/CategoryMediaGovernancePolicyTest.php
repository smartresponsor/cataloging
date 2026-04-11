<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 * Author: Oleksandr Tishchenko <dev@highhopesamerica.com>.
 */

namespace App\Tests\Category;

use App\Policy\CategoryMediaGovernancePolicy;
use App\ValueObject\CatalogAuditContext;
use App\ValueObject\CategoryMediaBindingScope;
use App\ValueObject\CategoryMediaBindingState;
use App\ValueObject\CategoryMediaBindRequest;
use PHPUnit\Framework\TestCase;

final class CategoryMediaGovernancePolicyTest extends TestCase
{
    public function testAssertBindingAllowedAcceptsCanonicalMediaPayload(): void
    {
        $policy = new CategoryMediaGovernancePolicy();

        $policy->assertBindingAllowed(
            new CategoryMediaBindRequest(
                new CategoryMediaBindingScope(
                    'binding-1',
                    'category-1',
                    'asset-1',
                    'primary',
                    ['storefront'],
                    ['en_US'],
                ),
                new CategoryMediaBindingState(true, true, []),
                new CatalogAuditContext('operator-1', 'bind primary category image'),
            ),
        );

        self::assertTrue(true);
    }

    public function testAssertBindingAllowedRejectsMissingChannels(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $policy = new CategoryMediaGovernancePolicy();
        $policy->assertBindingAllowed(
            new CategoryMediaBindRequest(
                new CategoryMediaBindingScope(
                    'binding-1',
                    'category-1',
                    'asset-1',
                    'primary',
                    [],
                    ['en_US'],
                ),
                new CategoryMediaBindingState(true, true, []),
                new CatalogAuditContext('operator-1', 'bind primary category image'),
            ),
        );
    }
}
