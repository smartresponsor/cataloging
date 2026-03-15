<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Tests\Infrastructure;

use App\Infra\Category\ProductWebhookPublisher;
use PHPUnit\Framework\TestCase;

final class CatalogCategoryWebhookPublisherTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $client = new class {
            public function request($m, $u, $o = [])
            {
            }
        };
        $pub = new ProductWebhookPublisher($client, 'http://example');
        $pub->publish(['event' => 'category.changed']);
        $this->assertTrue(true);
    }
}
