<?php

# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Tests\DataFixtures;

use App\Cataloging\DataFixtures\CategoryFixtures;
use App\Cataloging\Entity\Catalog\CatalogCategoryEntity;
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
        $manager->expects(self::exactly(46))
            ->method('persist')
            ->willReturnCallback(static function (object $entity) use (&$persisted): void {
                ++$persisted;
                if ($entity instanceof CatalogCategoryEntity && 0 === $entity->getId()) {
                    $property = new \ReflectionProperty(CatalogCategoryEntity::class, 'id');
                    $property->setValue($entity, $persisted);
                }
            });
        $manager->expects(self::exactly(26))->method('flush');

        (new CategoryFixtures())->load($manager);

        self::assertSame(46, $persisted);
    }
}
