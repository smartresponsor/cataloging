<?php

declare(strict_types=1);

namespace App\Cataloging\Repository\Catalog;

use App\Cataloging\Entity\Catalog\CatalogCategoryTranslationEntity;
use App\Cataloging\RepositoryInterface\Catalog\CatalogCategoryTranslationRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<CatalogCategoryTranslationEntity> */
final class CatalogCategoryTranslationRepository extends ServiceEntityRepository implements CatalogCategoryTranslationRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CatalogCategoryTranslationEntity::class);
    }
}
