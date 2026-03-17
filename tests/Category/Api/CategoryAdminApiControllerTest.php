<?php

declare(strict_types=1);
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp.
 */

namespace App\Tests\Category\Api;

use App\Controller\Api\CategoryAdminApiController;
use App\Repository\CategoryRepository;
use App\Service\BulkOperator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

final class CategoryAdminApiControllerTest extends TestCase
{
    public function testListReturnsAdminEnvelopeAndIncludesDraftRows(): void
    {
        $repository = new CategoryRepository();
        $repository->seed([
            ['id' => 'root', 'taxonomyId' => 'catalog', 'parentId' => null, 'name' => ['en' => 'Root'], 'slug' => ['en' => 'root'], 'meta' => ['published' => true]],
            ['id' => 'electronics', 'taxonomyId' => 'catalog', 'parentId' => 'root', 'name' => ['en' => 'Electronics'], 'slug' => ['en' => 'electronics'], 'meta' => ['published' => true]],
            ['id' => 'hidden', 'taxonomyId' => 'catalog', 'parentId' => 'root', 'name' => ['en' => 'Hidden'], 'slug' => ['en' => 'hidden'], 'meta' => ['published' => false]],
        ]);

        $controller = new CategoryAdminApiController(new BulkOperator($repository), $repository);
        $response = $controller->list(new Request(['taxonomy' => 'catalog', 'locale' => 'en', 'depth' => 3]));
        $payload = json_decode((string) $response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(200, $response->getStatusCode());
        self::assertTrue($payload['ok']);
        self::assertSame(3, $payload['count']);
        self::assertTrue($payload['pageInfo']['includeDrafts']);
        self::assertSame(['root', 'electronics', 'hidden'], array_column($payload['items'], 'id'));
    }

    public function testBulkRejectsInvalidAction(): void
    {
        $controller = new CategoryAdminApiController();
        $response = $controller->bulk(new Request(content: json_encode(['ids' => ['cat-1'], 'action' => 'drop-all'], JSON_THROW_ON_ERROR)));
        $payload = json_decode((string) $response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(400, $response->getStatusCode());
        self::assertFalse($payload['ok']);
        self::assertSame(['action is invalid'], $payload['error']);
    }

    public function testBulkPublishReturnsPublicReadProof(): void
    {
        $repository = new CategoryRepository();
        $repository->seed([
            ['id' => 'root', 'taxonomyId' => 'catalog', 'parentId' => null, 'name' => ['en' => 'Root'], 'slug' => ['en' => 'root'], 'meta' => ['published' => true]],
            ['id' => 'hidden', 'taxonomyId' => 'catalog', 'parentId' => 'root', 'name' => ['en' => 'Hidden'], 'slug' => ['en' => 'hidden'], 'meta' => ['published' => false]],
        ]);

        $controller = new CategoryAdminApiController(new BulkOperator($repository), $repository);
        $response = $controller->bulk(new Request(content: json_encode(['ids' => ['hidden'], 'action' => 'publish'], JSON_THROW_ON_ERROR)));
        $payload = json_decode((string) $response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(200, $response->getStatusCode());
        self::assertTrue($payload['ok']);
        self::assertSame(2, $payload['publicCountAfter']);
        self::assertSame(['root', 'hidden'], $payload['publicIdsAfter']);
    }
}
