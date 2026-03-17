<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Tests\Category\Api;

use App\Controller\CategoryCollectionController;
use App\Service\CollectionBuilder;
use App\Service\RuleEngine;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

final class CategoryCollectionControllerTest extends TestCase
{
    public function testInvokeReturnsCanonicalCollectionEnvelope(): void
    {
        $controller = new CategoryCollectionController(new CollectionBuilder(new RuleEngine()));
        $request = new Request(content: json_encode(['merchant' => 'default', 'locale' => 'en'], JSON_THROW_ON_ERROR));

        $response = $controller($request);
        $payload = json_decode((string) $response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(200, $response->getStatusCode());
        self::assertTrue($payload['ok']);
        self::assertSame('en', $payload['locale']);
        self::assertSame(2, $payload['rulesCount']);
        self::assertSame(2, $payload['count']);
        self::assertSame('root', $payload['data'][0]['slug']);
    }
}
