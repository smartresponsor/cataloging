<?php

declare(strict_types=1);

namespace App\Cataloging\Repository\Catalog;

use App\Cataloging\Entity\Catalog\CatalogCategoryProductBindingEntity;
use App\Cataloging\RepositoryInterface\Catalog\CatalogCategoryProductBindingRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<CatalogCategoryProductBindingEntity> */
final class CatalogCategoryProductBindingRepository extends ServiceEntityRepository implements CatalogCategoryProductBindingRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CatalogCategoryProductBindingEntity::class);
    }
}
