<?php

declare(strict_types=1);

namespace App\Tests\Category\Infrastructure;

use App\Infrastructure\CategoryRepositoryStateStore;
use App\Repository\CategoryRepository;
use PHPUnit\Framework\TestCase;

final class CategoryRepositoryStateStoreTest extends TestCase
{
    public function testSaveAndLoadRoundTripPreservesPublishedState(): void
    {
        $file = sys_get_temp_dir().'/category-state-'.bin2hex(random_bytes(4)).'.json';
        @unlink($file);

        $repository = new CategoryRepository();
        $repository->seed([
            ['id' => 'root', 'taxonomyId' => 'catalog', 'parentId' => null, 'name' => ['en' => 'Root'], 'slug' => ['en' => 'root'], 'meta' => ['published' => true]],
            ['id' => 'hidden', 'taxonomyId' => 'catalog', 'parentId' => 'root', 'name' => ['en' => 'Hidden'], 'slug' => ['en' => 'hidden'], 'meta' => ['published' => false]],
        ]);
        $repository->setPublished('hidden', true, 'tester');

        $store = new CategoryRepositoryStateStore();
        $saved = $store->save($repository, $file);

        self::assertSame($file, $saved['file']);
        self::assertFileExists($file);

        $reloaded = new CategoryRepository();
        $loaded = $store->load($reloaded, $file);

        self::assertTrue($loaded['loaded']);
        self::assertTrue($reloaded->findById('hidden', 'en')['meta']['published']);
        self::assertSame(['root', 'hidden'], array_column($reloaded->publishedTree('catalog', null, 5, 'en'), 'id'));

        @unlink($file);
    }
}
