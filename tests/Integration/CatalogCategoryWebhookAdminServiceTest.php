<?php

declare(strict_types=1);

// Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp

namespace App\Tests\Integration;

use App\Service\Integration\Category\WebhookAdminService;
use PHPUnit\Framework\TestCase;

final class CatalogCategoryWebhookAdminServiceTest extends TestCase
{
    public function testRegisterAndScheduleDelivery(): void
    {
        $service = new WebhookAdminService();

        $key = $service->registerKey('default');
        $deliveryId = $service->scheduleDelivery('https://example.test/webhook', ['event' => 'category.publish']);

        self::assertSame('default', $key['name']);
        self::assertIsString($key['token']);
        self::assertGreaterThan(0, $deliveryId);
    }
}
