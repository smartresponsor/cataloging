<?php

declare(strict_types=1);

namespace App\Tests\Category\Command;

use App\Command\DumpCategoryTreeCommand;
use App\Infrastructure\CategoryRepositoryStateStore;
use App\Repository\CategoryRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class DumpCategoryTreeCommandTest extends TestCase
{
    public function testDumpUsesPersistedStateAndReturnsOnlyPublishedRowsByDefault(): void
    {
        $stateFile = sys_get_temp_dir().'/category-tree-dump-'.bin2hex(random_bytes(4)).'.json';
        @unlink($stateFile);

        $seed = new CategoryRepository();
        $seed->seed([
            ['id' => 'root', 'taxonomyId' => 'catalog', 'parentId' => null, 'name' => ['en' => 'Root'], 'slug' => ['en' => 'root'], 'meta' => ['published' => true]],
            ['id' => 'visible', 'taxonomyId' => 'catalog', 'parentId' => 'root', 'name' => ['en' => 'Visible'], 'slug' => ['en' => 'visible'], 'meta' => ['published' => true]],
            ['id' => 'draft', 'taxonomyId' => 'catalog', 'parentId' => 'root', 'name' => ['en' => 'Draft'], 'slug' => ['en' => 'draft'], 'meta' => ['published' => false]],
        ]);

        $store = new CategoryRepositoryStateStore();
        $store->save($seed, $stateFile);

        $command = new DumpCategoryTreeCommand(new CategoryRepository(), $store);
        $tester = new CommandTester($command);
        $status = $tester->execute(['taxonomy' => 'catalog', '--state-file' => $stateFile]);

        self::assertSame(0, $status);
        $payload = json_decode(trim($tester->getDisplay()), true, 512, JSON_THROW_ON_ERROR);
        self::assertTrue($payload['ok']);
        self::assertFalse($payload['includeDrafts']);
        self::assertSame(['root', 'visible'], $payload['ids']);

        @unlink($stateFile);
    }

    public function testDumpCanIncludeDraftRows(): void
    {
        $repository = new CategoryRepository();
        $repository->seed([
            ['id' => 'root', 'taxonomyId' => 'catalog', 'parentId' => null, 'name' => ['en' => 'Root'], 'slug' => ['en' => 'root'], 'meta' => ['published' => true]],
            ['id' => 'draft', 'taxonomyId' => 'catalog', 'parentId' => 'root', 'name' => ['en' => 'Draft'], 'slug' => ['en' => 'draft'], 'meta' => ['published' => false]],
        ]);

        $command = new DumpCategoryTreeCommand($repository, new CategoryRepositoryStateStore());
        $tester = new CommandTester($command);
        $status = $tester->execute(['taxonomy' => 'catalog', '--include-drafts' => true]);

        self::assertSame(0, $status);
        $payload = json_decode(trim($tester->getDisplay()), true, 512, JSON_THROW_ON_ERROR);
        self::assertTrue($payload['includeDrafts']);
        self::assertSame(['root', 'draft'], $payload['ids']);
    }
}
