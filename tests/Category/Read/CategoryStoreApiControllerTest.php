<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Tests\Category\Read;

use App\Controller\CategoryStoreApiController;
use App\Service\ChannelFilter;
use App\Service\ReadOptimizer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

final class CategoryStoreApiControllerTest extends TestCase
{
    public function testStoreApiReturnsCanonicalEnvelope(): void
    {
        $controller = new CategoryStoreApiController(new ChannelFilter(), new ReadOptimizer());
        $request = new Request(['channel' => 'default']);

        $response = $controller($request);
        $payload = json_decode((string) $response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(200, $response->getStatusCode());
        self::assertArrayHasKey('data', $payload);
        self::assertArrayHasKey('count', $payload);
        self::assertSame('default', $payload['channel']);
        self::assertGreaterThanOrEqual(3, $payload['count']);
    }

    public function testStoreApiFiltersByLocale(): void
    {
        $controller = new CategoryStoreApiController(new ChannelFilter(), new ReadOptimizer());
        $request = new Request(['channel' => 'default', 'locale' => 'en']);

        $response = $controller($request);
        $payload = json_decode((string) $response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(2, $payload['count']);
        self::assertSame('en', $payload['locale']);
    }
}
