<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Tests\Category\Read;

use App\Controller\CategorySearchController;
use App\Service\SearchService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

final class CategorySearchControllerTest extends TestCase
{
    public function testSearchReturnsCanonicalEnvelope(): void
    {
        $controller = new CategorySearchController(new SearchService());
        $response = $controller(new Request(['q' => 'root']));
        $payload = json_decode((string) $response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('root', $payload['query']);
        self::assertSame(1, $payload['count']);
        self::assertSame('Root', $payload['items'][0]['name']);
        self::assertArrayHasKey('locale', $payload['facets']);
    }

    public function testSearchReturnsEmptyResultSetForUnknownQuery(): void
    {
        $controller = new CategorySearchController(new SearchService());
        $response = $controller(new Request(['q' => 'unknown-category']));
        $payload = json_decode((string) $response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(0, $payload['count']);
        self::assertSame([], $payload['items']);
    }
}
