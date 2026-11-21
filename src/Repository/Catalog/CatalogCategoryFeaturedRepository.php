<?php

declare(strict_types=1);

namespace App\Cataloging\Repository\Catalog;

use App\Cataloging\Entity\Catalog\CatalogCategoryFeaturedEntity;
use App\Cataloging\RepositoryInterface\Catalog\CatalogCategoryFeaturedRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<CatalogCategoryFeaturedEntity> */
final class CatalogCategoryFeaturedRepository extends ServiceEntityRepository implements CatalogCategoryFeaturedRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CatalogCategoryFeaturedEntity::class);
    }
}
