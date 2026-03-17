<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Tests\Category\Read;

use App\Controller\CategoryStorefrontController;
use App\Service\ChannelFilter;
use App\Service\ReadOptimizer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

final class CategoryStorefrontControllerTest extends TestCase
{
    public function testStorefrontReturnsPublishedDefaultChannelRows(): void
    {
        $controller = new CategoryStorefrontController(new ReadOptimizer(), new ChannelFilter());

        $response = $controller(new Request());
        $payload = json_decode((string) $response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('default', $payload['channel']);
        self::assertSame(3, $payload['count']);
        self::assertSame('Root', $payload['data'][0]['name']);
    }

    public function testStorefrontFiltersByLocaleAndExcludesUnpublishedRows(): void
    {
        $controller = new CategoryStorefrontController(new ReadOptimizer(), new ChannelFilter());
        $request = new Request(['locale' => 'uk']);

        $response = $controller($request);
        $payload = json_decode((string) $response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(1, $payload['count']);
        self::assertSame('uk', $payload['locale']);
        self::assertSame('phones', $payload['data'][0]['slug']);
    }
}
