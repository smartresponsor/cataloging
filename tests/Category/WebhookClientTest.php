<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Tests\Category;

use App\Service\WebhookClient;
use PHPUnit\Framework\TestCase;

final class WebhookClientTest extends TestCase
{
    public function testDispatchReturnsTrueForDefaultHappyPath(): void
    {
        $client = new WebhookClient('secret-key');

        self::assertTrue($client->send('https://example.test/hook', 'category.changed', ['id' => 'cat-1']));
    }
}
