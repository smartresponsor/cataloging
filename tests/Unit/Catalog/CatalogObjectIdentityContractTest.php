<?php

declare(strict_types=1);

namespace App\Cataloging\Tests\Unit\Catalog;

use App\Cataloging\Entity\Catalog\CatalogCategoryFeaturedEntity;
use App\Objecting\Embeddable\ObjectIdentityEmbeddable;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\Persistence\Mapping\Driver\MappingDriverChain;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV7;

#[CoversClass(CatalogCategoryFeaturedEntity::class)]
#[CoversClass(ObjectIdentityEmbeddable::class)]
final class CatalogObjectIdentityContractTest extends TestCase
{
    public function testCatalogCategoryFeaturedUsesCanonicalObjectIdentityContract(): void
    {
        $featured = new CatalogCategoryFeaturedEntity('01KCN8RKRGF8S9RRDZ6YH0X09M', 'home');
        $objectUuid = $featured->getObjectUuid();

        self::assertSame(26, \strlen($objectUuid));
        self::assertInstanceOf(UuidV7::class, Uuid::fromString($objectUuid));
        self::assertMatchesRegularExpression('/^[0-9A-HJKMNP-TV-Z]{26}$/', $objectUuid);
        self::assertSame('01KCN8RKRGF8S9RRDZ6YH0X09M-home', $featured->getObjectSlug());

        $featured->setObjectSlug('catalog-featured-readable-slug');

        self::assertSame($objectUuid, $featured->getObjectUuid());
        self::assertSame('catalog-featured-readable-slug', $featured->getObjectSlug());
    }

    public function testCatalogCategoryFeaturedDoctrineMappingUsesBinaryUuidAndMandatorySlug(): void
    {
        $configuration = ORMSetup::createAttributeMetadataConfiguration([], true);
        $configuration->enableNativeLazyObjects(true);
        $driverChain = new MappingDriverChain();
        $catalogDriver = ORMSetup::createAttributeMetadataConfiguration([
            dirname(__DIR__, 3).'/src/Entity',
        ], true)->getMetadataDriverImpl();
        self::assertNotNull($catalogDriver);
        $driverChain->addDriver($catalogDriver, 'App\\Cataloging\\Entity');
        $objectingDriver = ORMSetup::createAttributeMetadataConfiguration([
            dirname(__DIR__, 4).'/Objecting/src/Embeddable',
        ], true)->getMetadataDriverImpl();
        self::assertNotNull($objectingDriver);
        $driverChain->addDriver($objectingDriver, 'App\\Objecting\\Embeddable');
        $configuration->setMetadataDriverImpl($driverChain);

        $entityManager = new EntityManager(DriverManager::getConnection([
            'driver' => 'pdo_sqlite',
            'memory' => true,
        ], $configuration), $configuration);

        $metadata = $entityManager->getClassMetadata(CatalogCategoryFeaturedEntity::class);
        $objectingMetadata = $entityManager->getClassMetadata(ObjectIdentityEmbeddable::class);

        self::assertSame(ObjectIdentityEmbeddable::class, $objectingMetadata->name);
        self::assertTrue($objectingMetadata->isEmbeddedClass);
        self::assertSame('binary', $objectingMetadata->getFieldMapping('objectUuid')['type']);
        self::assertSame(16, $objectingMetadata->getFieldMapping('objectUuid')['length']);
        self::assertFalse($objectingMetadata->getFieldMapping('objectUuid')['nullable'] ?? false);
        self::assertSame('string', $objectingMetadata->getFieldMapping('objectSlug')['type']);
        self::assertSame(190, $objectingMetadata->getFieldMapping('objectSlug')['length']);
        self::assertFalse($objectingMetadata->getFieldMapping('objectSlug')['nullable'] ?? false);

        $objectUuid = $metadata->getFieldMapping('objectIdentity.objectUuid');
        $objectSlug = $metadata->getFieldMapping('objectIdentity.objectSlug');

        self::assertArrayHasKey('objectIdentity', $metadata->embeddedClasses);
        self::assertSame(ObjectIdentityEmbeddable::class, $metadata->embeddedClasses['objectIdentity']['class']);
        self::assertSame('object_uuid', $objectUuid['columnName']);
        self::assertSame('object_slug', $objectSlug['columnName']);
        self::assertSame('binary', $objectUuid['type']);
        self::assertSame(16, $objectUuid['length']);
        self::assertFalse($objectUuid['nullable'] ?? false);
        self::assertSame('string', $objectSlug['type']);
        self::assertSame(190, $objectSlug['length']);
        self::assertFalse($objectSlug['nullable'] ?? false);

        $schemaTool = new SchemaTool($entityManager);
        $createSql = \implode("\n", $schemaTool->getCreateSchemaSql([$metadata]));

        self::assertNotSame('', $createSql);
        self::assertStringContainsString('object_uuid', $createSql);
        self::assertStringContainsString('object_slug', $createSql);
    }
}
