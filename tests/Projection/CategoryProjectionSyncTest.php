<?php

declare(strict_types=1);

namespace App\Cataloging\Tests\Projection;

use App\Cataloging\Entity\Catalog\CatalogCategoryEntity;
use App\Cataloging\Projection\CategoryProjectionSync;
use App\Cataloging\Service\CatalogCategoryProjectionSynchronizerService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class CategoryProjectionSyncTest extends TestCase
{
    public static function invalidEvents(): iterable
    {
        yield 'missing type' => [['payload' => ['categoryId' => '1']]];
        yield 'unknown type' => [['type' => 'category.deleted', 'payload' => ['categoryId' => '1']]];
        yield 'missing payload' => [['type' => 'category.moved']];
        yield 'missing id' => [['type' => 'category.published', 'payload' => []]];
        yield 'empty id' => [['type' => 'category.unpublished', 'payload' => ['categoryId' => ' ']]];
        yield 'array id' => [['type' => 'category.moved', 'payload' => ['categoryId' => []]]];
    }

    #[DataProvider('invalidEvents')]
    public function testInvalidEventsFailClosedBeforeDatabaseAccess(array $event): void
    {
        $sync = (new \ReflectionClass(CategoryProjectionSync::class))->newInstanceWithoutConstructor();
        $this->expectException(\InvalidArgumentException::class);
        $sync->apply($event);
    }

    public function testMissingCategoryFailsInsteadOfAcknowledgingEvent(): void
    {
        $repository = $this->createMock(EntityRepository::class);
        $repository->expects(self::once())->method('find')->with('42')->willReturn(null);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects(self::once())->method('getRepository')
            ->with(CatalogCategoryEntity::class)->willReturn($repository);
        $registry = $this->createMock(ManagerRegistry::class);
        $sync = new CategoryProjectionSync($entityManager, new CatalogCategoryProjectionSynchronizerService($registry));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Category 42 not found');
        $sync->apply(['type' => 'category.published', 'payload' => ['categoryId' => '42']]);
    }
}
