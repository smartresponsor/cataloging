<?php

declare(strict_types=1);

namespace App\Tests\Category\Admin;

use App\Controller\Admin\CategoryBulkController;
use App\Repository\CategoryRepository;
use App\Service\BulkOperator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

final class CategoryBulkControllerTest extends TestCase
{
    public function testBulkReturnsCanonicalEnvelope(): void
    {
        $controller = new CategoryBulkController(new BulkOperator());
        $request = new Request(content: json_encode([
            'ids' => [1, 2, 'bad'],
            'action' => 'publish',
        ], JSON_THROW_ON_ERROR));

        $response = $controller($request);
        $payload = json_decode((string) $response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(200, $response->getStatusCode());
        self::assertTrue($payload['ok']);
        self::assertSame('publish', $payload['action']);
        self::assertSame(2, $payload['successCount']);
        self::assertSame(1, $payload['failedCount']);
    }

    public function testBulkRejectsInvalidAction(): void
    {
        $controller = new CategoryBulkController(new BulkOperator());
        $request = new Request(content: json_encode([
            'ids' => [1],
            'action' => 'destroy-world',
        ], JSON_THROW_ON_ERROR));

        $response = $controller($request);
        $payload = json_decode((string) $response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(400, $response->getStatusCode());
        self::assertFalse($payload['ok']);
        self::assertSame(['action is invalid'], $payload['error']);
    }

    public function testBulkUsesRepositoryBackedPublishingForStringIds(): void
    {
        $repository = new CategoryRepository();
        $repository->seed([
            ['id' => 'root', 'taxonomyId' => 'catalog', 'parentId' => null, 'name' => ['en' => 'Root'], 'slug' => ['en' => 'root'], 'meta' => ['published' => true]],
            ['id' => 'hidden', 'taxonomyId' => 'catalog', 'parentId' => 'root', 'name' => ['en' => 'Hidden'], 'slug' => ['en' => 'hidden'], 'meta' => ['published' => false]],
        ]);

        $controller = new CategoryBulkController(new BulkOperator($repository));
        $request = new Request(content: json_encode([
            'ids' => ['hidden'],
            'action' => 'publish',
        ], JSON_THROW_ON_ERROR));

        $response = $controller($request);
        $payload = json_decode((string) $response->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertSame(200, $response->getStatusCode());
        self::assertTrue($payload['ok']);
        self::assertSame(1, $payload['successCount']);
        self::assertTrue($repository->findById('hidden')['meta']['published']);
    }
}
