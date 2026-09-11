<?php

# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Tests\DataFixtures;

use App\Cataloging\DataFixtures\CategoryFixtures;
use App\Cataloging\Entity\Catalog\CatalogCategoryEntity;
use App\Cataloging\Service\CatalogCategoryProjectionSynchronizerService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\TestCase;

final class CategoryFixturesTest extends TestCase
{
    private function skipWhenUidComponentMissing(): void
    {
        if (!class_exists(\Symfony\Component\Uid\Ulid::class)) {
            self::markTestSkipped('symfony/uid is not installed in this environment.');
        }
    }

    public function testLoadPersistsDatasetAndFlushes(): void
    {
        $this->skipWhenUidComponentMissing();

        $persisted = 0;
        $manager = $this->createMock(ObjectManager::class);
        $manager->expects(self::atLeastOnce())
            ->method('persist')
            ->willReturnCallback(static function (object $entity) use (&$persisted): void {
                ++$persisted;
                if ($entity instanceof CatalogCategoryEntity && 0 === $entity->getId()) {
                    $property = new \ReflectionProperty(CatalogCategoryEntity::class, 'id');
                    $property->setValue($entity, $persisted);
                }
            });
        $manager->expects(self::exactly(27))->method('flush');

        $projectionRepository = $this->createMock(EntityRepository::class);
        $projectionRepository->method('find')->willReturn(null);
        $projectionEntityManager = $this->createMock(EntityManagerInterface::class);
        $projectionEntityManager->method('getRepository')->willReturn($projectionRepository);
        $registry = $this->createMock(ManagerRegistry::class);
        $registry->method('getManager')->willReturn($projectionEntityManager);
        $projectionSynchronizer = new CatalogCategoryProjectionSynchronizerService($registry);

        (new CategoryFixtures($projectionSynchronizer))->load($manager);

        self::assertGreaterThanOrEqual(65, $persisted);
    }
}
