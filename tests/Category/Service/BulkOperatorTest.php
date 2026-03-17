<?php

declare(strict_types=1);

namespace App\Tests\Category\Service;

use App\Repository\CategoryRepository;
use App\Service\BulkOperator;
use PHPUnit\Framework\TestCase;

final class BulkOperatorTest extends TestCase
{
    public function testRunPublishesStringIdsWhenRepositoryIsInjected(): void
    {
        $repository = new CategoryRepository();
        $repository->seed([
            ['id' => 'root', 'taxonomyId' => 'catalog', 'parentId' => null, 'name' => ['en' => 'Root'], 'slug' => ['en' => 'root'], 'meta' => ['published' => true]],
            ['id' => 'hidden', 'taxonomyId' => 'catalog', 'parentId' => 'root', 'name' => ['en' => 'Hidden'], 'slug' => ['en' => 'hidden'], 'meta' => ['published' => false]],
        ]);

        $result = (new BulkOperator($repository))->run(['hidden', 'missing'], 'publish');

        self::assertCount(1, $result['success']);
        self::assertCount(1, $result['failed']);
        self::assertSame('hidden', $result['success'][0]['id']);
        self::assertTrue($result['success'][0]['published']);
        self::assertTrue($repository->findById('hidden')['meta']['published']);
    }
}
