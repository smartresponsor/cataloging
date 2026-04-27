<?php

declare(strict_types=1);

namespace App\Cataloging\Tests\Support;

use App\Cataloging\Doctrine\Type\LtreeType;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMSetup;

final class CategoryDoctrineEntityManagerFactory
{
    public static function createConnection(): Connection
    {
        return DriverManager::getConnection([
            'driver' => 'pdo_sqlite',
            'memory' => true,
        ]);
    }

    public static function createEntityManager(Connection $connection): EntityManagerInterface
    {
        if (!Type::hasType(LtreeType::NAME)) {
            Type::addType(LtreeType::NAME, LtreeType::class);
        }

        $config = ORMSetup::createAttributeMetadataConfiguration(
            [dirname(__DIR__, 2).'/src/Entity/Catalog'],
            true,
        );
        $config->enableNativeLazyObjects(true);

        return new EntityManager($connection, $config);
    }
}
